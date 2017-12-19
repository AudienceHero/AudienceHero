import HttpError from './HttpError';

export function apiFetch(path, auth, params, options = {}) {
    const defaultOptions = {
        headers: new Headers({
            Accept: 'application/ld+json',
            'Content-Type': 'application/json',
        }),
    };

    if (auth) {
        const token = localStorage.getItem('token');
        defaultOptions.headers.append('Authorization', `Bearer ${token}`);
    }
    if (params) {
        defaultOptions.body = JSON.stringify(params);
    }

    const p = fetch(`/api/${path}`, Object.assign(defaultOptions, options))
        .then(response =>
            response.text().then(text => ({
                status: response.status,
                statusText: response.statusText,
                headers: response.headers,
                body: text,
            }))
        )
        .then(({ status, statusText, headers, body }) => {
            let json;
            try {
                json = JSON.parse(body);
            } catch (e) {
                // not json, no big deal
            }

            if (status < 200 || status >= 300) {
                return Promise.reject(
                    new HttpError(
                        (json && json.message) || statusText,
                        status,
                        json
                    )
                );
            }

            return { status, statusText, headers, body, json };
        });

    return p;
}

export default apiFetch;
