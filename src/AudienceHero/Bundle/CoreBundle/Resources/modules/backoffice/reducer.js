import {
    I18N_COUNTRY_LIST_SUCCESS,
    I18N_LANGUAGE_LIST_SUCCESS,
} from './actions';
import { combineReducers } from 'redux';

export const i18n = (
    state = { countries: [], languages: [] },
    { type, payload }
) => {
    switch (type) {
        case I18N_COUNTRY_LIST_SUCCESS:
            const countries = state.countries.slice();
            countries[payload.locale] = payload.countries;

            return {
                ...state,
                countries,
            };
        case I18N_LANGUAGE_LIST_SUCCESS:
            const languages = state.languages.slice();
            languages[payload.locale] = payload.languages;

            return {
                ...state,
                languages,
            };
        default:
            return state;
    }
};

export default combineReducers({ i18n });
