const entrypoint = '';

export function apiFetch(path, options = {}) {
    const defaultOptions = {
        headers: new Headers({
            Accept: 'application/ld+json',
            'Content-Type': 'application/json',
        }),
    };

    const p = fetch(
        `${entrypoint}/api/${path}`,
        Object.assign(defaultOptions, options)
    )
        .then(response =>
            response.text().then(text => ({
                status: response.status,
                statusText: response.statusText,
                headers: response.headers,
                body: text,
            }))
        )
        .then(({ status, statusText, headers, body }) => {
            const json = JSON.parse(body);
            if (status < 200 || status >= 300) {
                return Promise.reject(json);
            }

            return { status, statusText, headers, body };
        });

    return p;
}
