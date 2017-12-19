import { FETCH_DATA } from '../actions/fetchActions';
import { Map } from 'immutable';
import isPlainObject from 'lodash.isplainobject';

export const transformJsonLd = (maxDepth = 3, depth = 1) => doc => {
    if (!isPlainObject(doc)) {
        return doc;
    }

    if ('undefined' !== typeof doc.id) {
        doc.originId = doc.id;
    }

    if (doc['@id']) {
        let id = doc['@id'];
        doc.id = id.substring(id.lastIndexOf('/') + 1);
    }
    doc['depth'] = depth;

    if (depth < maxDepth) {
        Object.keys(doc).forEach(key => {
            doc[key] = transformJsonLd(maxDepth, depth + 1)(doc[key]);
        });
    }

    return doc;
};

export default (previousState = new Map(), { type, payload }) => {
    switch (type) {
        case `${FETCH_DATA}_SUCCESS`:
            var data = transformJsonLd()(payload);
            if (data['@id']) {
                return previousState.set(data.id, data);
            }

            return previousState;
        default:
            return previousState;
    }
};
