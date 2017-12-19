import { ACTION } from '@audiencehero/common';
import { CREATE } from 'react-admin';

export const USER_REGISTER = 'USER_REGISTER';
export const userRegister = ({ username, password, email }) => ({
    type: USER_REGISTER,
    payload: { username, password, email },
});

export const USER_REGISTER_FAILURE = 'USER_REGISTER_FAILURE';
export const userRegisterFailure = json => ({
    type: USER_REGISTER_FAILURE,
    payload: json,
});
export const USER_REGISTER_SUCCESS = 'USER_REGISTER_SUCCESS';
export const userRegisterSuccess = ({ username, password }) => ({
    type: USER_REGISTER_SUCCESS,
    payload: { username, password },
});

export const PERSON_EMAIL_SEND_VERIFICATION_EMAIL =
    'PERSON_EMAIL_SEND_VERIFICATION_EMAIL';
export const sendPersonEmailVerification = id => ({
    type: PERSON_EMAIL_SEND_VERIFICATION_EMAIL,
    payload: { action: 'send-verification-email', id },
    meta: { resource: 'person_emails', fetch: ACTION },
});

export const PERSON_EMAIL_VERIFY = 'PERSON_EMAIL_VERIFY';
export const verifyPersonEmail = (id, confirmationToken) => ({
    type: PERSON_EMAIL_VERIFY,
    payload: { action: 'verify', id, data: { token: confirmationToken } },
    meta: { resource: 'person_emails', fetch: ACTION },
});

export const USER_PASSWORD_RESET_REQUEST = 'USER_FORGOTTEN_PASSWORD_REQUEST';
export const resetUserPasswordRequest = payload => ({
    type: USER_PASSWORD_RESET_REQUEST,
    payload,
});

export const USER_PASSWORD_RESET = 'USER_PASSWORD_RESET';
export const resetUserPassword = ({ plainPassword, confirmationToken }) => ({
    type: USER_PASSWORD_RESET,
    payload: { plainPassword, confirmationToken },
});

export const I18N_COUNTRY_LIST = 'I18N_COUNTRY_LIST';
export const fetchCountryList = locale => ({
    type: I18N_COUNTRY_LIST,
    payload: {
        locale,
    },
});

export const I18N_COUNTRY_LIST_SUCCESS = 'I18N_COUNTRY_LIST_SUCCESS';
export const fetchCountryListSuccess = (locale, countries) => ({
    type: I18N_COUNTRY_LIST_SUCCESS,
    payload: {
        locale,
        countries,
    },
});

export const I18N_LANGUAGE_LIST = 'I18N_LANGUAGE_LIST';
export const fetchLanguageList = locale => ({
    type: I18N_LANGUAGE_LIST,
    payload: {
        locale,
    },
});

export const I18N_LANGUAGE_LIST_SUCCESS = 'I18N_LANGUAGE_LIST_SUCCESS';
export const fetchLanguageListSuccess = (locale, languages) => ({
    type: I18N_LANGUAGE_LIST_SUCCESS,
    payload: {
        locale,
        languages,
    },
});
