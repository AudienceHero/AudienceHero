import { AUTH_LOGIN, AUTH_LOGOUT, AUTH_ERROR } from 'react-admin';

// TODO: Change this depending of environment
const entrypoint = '';

export default (type, params) => {
    switch (type) {
        case AUTH_LOGIN:
            const { username, password } = params;
            let formData = new FormData();
            formData.append('_username', username);
            formData.append('_password', password);
            const request = new Request(`${entrypoint}/login_check`, {
                method: 'POST',
                body: formData,
            });

            return fetch(request)
                .then(response => {
                    if (response.status < 200 || response.status >= 300) {
                        throw new Error(response.statusText);
                    }

                    return response.json();
                })
                .then(({ token }) => {
                    localStorage.setItem('not_authenticated', false);
                    localStorage.setItem('token', token); // The JWT token is stored in the browser's local storage
                });

        case AUTH_LOGOUT:
            localStorage.setItem('not_authenticated', true);
            localStorage.removeItem('token');
            break;

        case AUTH_ERROR:
            if (401 === params.status || 403 === params.status) {
                localStorage.setItem('not_authenticated', true);
                localStorage.removeItem('token');

                return Promise.reject();
            }
            break;

        default:
            break;
    }

    return Promise.resolve();
};
