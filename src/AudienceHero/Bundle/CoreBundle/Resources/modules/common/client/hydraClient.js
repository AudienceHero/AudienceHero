import {
    GET_LIST,
    GET_ONE,
    GET_MANY,
    GET_MANY_REFERENCE,
    CREATE,
    UPDATE,
    DELETE,
} from 'react-admin';
import isPlainObject from 'lodash.isplainobject';
import { stringify } from 'querystrings';
import fetchHydra from './fetchHydra';

export const ACTION = 'AH/ACTION';

export const flatten = doc => {
    console.log('pre', doc);
    Object.keys(doc).forEach(key => {
        if (key === 'subjects' || key === 'publicMetadata' || key === 'privateMetadata' || key === 'urls') {
            return;
        }

        let value = doc[key];
        if (!isPlainObject(value)) {
            return;
        }

        if (!value['@id']) {
            return;
        }

        doc[key] = value['@id'];
    });

    console.log('post', doc);
    return doc;
}

/**
 * Transform a Json-ld document to an Admin On Rest compatible document.
 *
 * @copyright Kevin Dunglas
 * @see https://github.com/api-platform/admin
 * @param {Number} depth
 * @param {Number} maxDepth
 */
export const transformJsonLdToAOR = (maxDepth = 4, depth = 1) => doc => {
    if (Array.isArray(doc)) {
        return doc.map(item => {
            return transformJsonLdToAOR(maxDepth, depth + 1)(item);
        });
    }

    if (!isPlainObject(doc)) {
        return doc;
    }

    if (depth < maxDepth) {
        Object.keys(doc).forEach(key => {
            doc[key] = transformJsonLdToAOR(maxDepth, depth + 1)(doc[key]);
        });
    }

    return doc;
};

/**
 * Maps react-admin queries to a Hydra powered REST API
 *
 * @copyright Kevin Dunglas
 * @see https://github.com/api-platform/admin
 * @see http://www.hydra-cg.com/
 * @example
 * GET_LIST     => GET http://my.api.url/posts
 * GET_ONE      => GET http://my.api.url/posts/123
 * GET_MANY     => GET http://my.api.url/posts/123, GET http://my.api.url/posts/456, GET http://my.api.url/posts/789
 * UPDATE       => PUT http://my.api.url/posts/123
 * CREATE       => POST http://my.api.url/posts/123
 * DELETE       => DELETE http://my.api.url/posts/123
 */
export default (apiUrl, httpClient = fetchHydra) => {
    /**
     * @param {String} type One of the constants appearing at the top if this file, e.g. 'UPDATE'
     * @param {String} resource Name of the resource to fetch, e.g. 'posts'
     * @param {Object} params The REST request params, depending on the type
     * @returns {Object} { url, options } The HTTP request parameters
     */
    const convertParametersToQuery = params => {
        const { page } = params.pagination;
        const { field, order } = params.sort;

        const orderParams = {};
        orderParams[field] = order;

        const normalizedFilters = {};
        for (const key of Object.keys(params.filter)) {
            var value = params.filter[key];
            var normalizedKey = key;

            if (!isPlainObject(value)) {
                normalizedFilters[normalizedKey] = value;
            } else {
                const extractKey = object => {
                    const extractedKey = Object.keys(object).first();
                    const extractedValue = object[extractedKey];

                    normalizedKey = `${normalizedKey}.${extractedKey}`;
                    if (!isPlainObject(extractedValue)) {
                        value = extractedValue;
                        return;
                    } else {
                        extractKey(extractedValue);
                    }
                };
                extractKey(value);
                normalizedFilters[normalizedKey] = value;
            }
        }

        const q = {
            ...normalizedFilters,
            page: page,
            order: orderParams,
        };

        if (params.perPage) {
            q['itemsPerPage'] = params.perPage;
        }

        return q;
    };

    const convertRESTRequestToHTTP = (type, resource, params) => {
        let url = '';
        const options = {};
        switch (type) {
            case GET_LIST: {
                url = `${apiUrl}/${resource}?${stringify(
                    convertParametersToQuery(params),
                    { arrayFormat: 'index' }
                )}`;
                break;
            }
            case GET_ONE:
                let id = params.id;
                if (isPlainObject(id)) {
                    id = id.id;
                }
                url = `${apiUrl}/${resource}/` + id;
                break;
            case GET_MANY_REFERENCE:
                url = `${apiUrl}/${resource}?${stringify({
                    [params.target]: params.id,
                })}`;
                break;
            case UPDATE:
                url = `${apiUrl}/${resource}/` + params.id;
                options.method = 'PUT';
                options.body = JSON.stringify(params.data);
                break;
            case ACTION:
                url = `${apiUrl}/${resource}/${params.id}/${params.action}`;
                options.method = 'PUT';
                options.body = JSON.stringify(params.data);
                break;
            case CREATE:
                url = `${apiUrl}/${resource}`;
                options.method = 'POST';
                options.body = JSON.stringify(params.data);
                break;
            case DELETE:
                url = `${apiUrl}/${resource}/` + params.id;
                options.method = 'DELETE';
                break;
            default:
                throw new Error(`Unsupported fetch action type ${type}`);
        }
        return { url, options };
    };

    /**
     * @param {Object} response - HTTP response from fetch()
     * @param {String} type     - One of the constants appearing at the top if this file, e.g. 'UPDATE'
     * @param {String} resource -  Name of the resource to fetch, e.g. 'posts'
     * @param {Object} params   - The REST request params, depending on the type
     * @returns {Object}        - REST response
     */
    const convertHTTPResponseToREST = (response, type, resource, params) => {
        switch (type) {
            case GET_ONE:
            case CREATE:
            case UPDATE:
                if (204 === response.status) {
                    return { data: {} };
                }

                return { data: flatten(transformJsonLdToAOR()(response.json)) };
            case GET_LIST:
                // TODO: support other prefixes than "hydra:"
                return {
                    data: response.json['hydra:member'].map(
                        transformJsonLdToAOR()
                    ),
                    total: response.json['hydra:totalItems'],
                };
            default:
                if (204 === response.status) {
                    return { data: {} };
                }

                return { data: transformJsonLdToAOR()(response.json) };
        }
    };

    /**
     * @param {string} type Request type, e.g GET_LIST
     * @param {string} resource Resource name, e.g. "posts"
     * @param {Object} payload Request parameters. Depends on the request type
     * @returns {Promise} the Promise for a REST response
     */
    return (type, resource, params) => {
        // Hydra doesn't handle WHERE IN requests, so we fallback to calling GET_ONE n times instead
        if (type === GET_MANY) {
            return Promise.all(
                params.ids.map(id => {
                    if (isPlainObject(id)) {
                        id = id.id;
                    }

                    return httpClient(`${apiUrl}/${resource}/${id}`);
                })
            ).then(responses => {
                const restResponse = { data: [] };
                restResponse.data = responses.map(response => response.json);
                restResponse.data.map(transformJsonLdToAOR());
                return restResponse;
            });
        }
        const { url, options } = convertRESTRequestToHTTP(
            type,
            resource,
            params
        );
        return httpClient(url, options).then(response =>
            convertHTTPResponseToREST(response, type, resource, params)
        );
    };
};
