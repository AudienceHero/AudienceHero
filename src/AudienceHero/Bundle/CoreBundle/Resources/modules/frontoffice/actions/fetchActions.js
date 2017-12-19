export const FETCH_DATA = 'AH/FETCH_DATA';
export const FETCH_START = 'AH/FETCH_START';
export const FETCH_END = 'AH/FETCH_END';
export const FETCH_ERROR = 'AH/FETCH_ERROR';
export const FETCH_CANCEL = 'AH/FETCH_CANCEL';

export const fetchData = (resource, id, auth = false) => ({
    type: FETCH_DATA,
    payload: {},
    meta: {
        fetch: FETCH_DATA,
        resource,
        id,
        auth,
        options: {},
    },
});
