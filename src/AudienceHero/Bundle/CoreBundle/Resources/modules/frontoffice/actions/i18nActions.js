import { FETCH_DATA } from './fetchActions';

export const FETCH_COUNTRIES = 'AH/FETCH_COUNTRIES';
export const fetchCountries = locale => ({
    type: FETCH_COUNTRIES,
    payload: {},
    meta: {
        fetch: FETCH_DATA,
        resource: `i18n/${locale}/countries`,
        auth: false,
        options: {},
    },
});
