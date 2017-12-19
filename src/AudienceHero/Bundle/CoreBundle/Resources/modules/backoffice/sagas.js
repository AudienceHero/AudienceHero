import { takeEvery, all, put, call, take } from 'redux-saga/effects';
import { buffers, eventChannel, END } from 'redux-saga';

import {
    CRUD_CREATE_FAILURE,
    userLogin as userLoginAction,
    showNotification,
} from 'react-admin';
import { push } from 'react-router-redux';
import {
    userRegisterFailure as userRegisterFailureAction,
    userRegisterSuccess as userRegisterSuccessAction,
    fetchCountryListSuccess as fetchCountryListSuccessAction,
    fetchLanguageListSuccess as fetchLanguageListSuccessAction,
    I18N_COUNTRY_LIST,
    I18N_LANGUAGE_LIST,
    USER_REGISTER,
    USER_REGISTER_FAILURE,
    USER_REGISTER_SUCCESS,
    PERSON_EMAIL_SEND_VERIFICATION_EMAIL,
    PERSON_EMAIL_VERIFY,
    USER_PASSWORD_RESET,
    USER_PASSWORD_RESET_REQUEST,
} from './actions';
import { apiFetch, transformViolations } from '@audiencehero/common';
import { stopSubmit } from 'redux-form';

export function* userRegister(action) {
    try {
        const data = action.payload;
        const body = JSON.stringify({
            username: data.username,
            email: data.email,
            plainPassword: data.password,
        });
        yield call(apiFetch, 'users', { method: 'POST', body });
        yield put(
            userRegisterSuccessAction({
                username: action.payload.username,
                password: action.payload.password,
            })
        );
    } catch (error) {
        yield put(userRegisterFailureAction(error));
    }
}

export function* userRegisterFailure(action) {
    yield put(showNotification('ah.user.registration.failure', 'warning'));
    yield put(stopSubmit('register', transformViolations(action.payload)));
}

export function* userRegisterSuccess(action) {
    yield put(showNotification('ah.user.registration.success'));
    yield put(userLoginAction(action.payload));
}

export function* personEmailSendVerificationEmailSuccess() {
    yield put(
        showNotification(
            'ah.person_email.notification.verification_email_sent.success'
        )
    );
}

export function* personEmailSendVerificationEmailFailure(error) {
    yield put(
        showNotification(
            'ah.person_email.notification.verification_email_sent.failure',
            'warning'
        )
    );
}

export function* personEmailVerifyFailure() {
    yield put(
        showNotification(
            'ah.person_email.notification.verification.failure',
            'warning'
        )
    );
    yield put(push('/person_emails'));
}

export function* personEmailVerifySuccess() {
    yield put(
        showNotification('ah.person_email.notification.verification.success')
    );
    yield put(push('/'));
}

export function* userPasswordResetRequest(action) {
    try {
        const body = JSON.stringify(action.payload);
        yield call(apiFetch, 'users/forgotten-password', {
            method: 'POST',
            body,
        });
        yield put(
            showNotification(
                'ah.user.notification.password_reset_request.success'
            )
        );
    } catch (error) {
        yield put(
            showNotification(
                'ah.user.notification.password_reset_request.failure',
                'warning'
            )
        );
    }
}

export function* userPasswordReset(action) {
    try {
        const body = JSON.stringify(action.payload);
        yield call(apiFetch, 'users/reset-password', { method: 'POST', body });
        yield put(
            showNotification('ah.user.notification.password_reset.success')
        );
        yield put(push('/login'));
    } catch (error) {
        yield put(
            showNotification(
                'ah.user.notification.password_reset.failure',
                'warning'
            )
        );
    }
}

export function* crudCreateFailure(action) {
    var json = action.payload;
    yield put(stopSubmit('record-form', transformViolations(json)));
}

export function* fetchCountryList(action) {
    try {
        var locale = action.payload.locale;
        let { body } = yield call(apiFetch, `i18n/${locale}/countries`, {
            method: 'GET',
        });
        const json = JSON.parse(body);
        yield put(fetchCountryListSuccessAction(locale, json));
    } catch (error) {
        yield put(
            showNotification('ah.i18n.countries.fetch.failure', 'warning')
        );
    }
}

export function* fetchLanguageList(action) {
    try {
        var locale = action.payload.locale;
        let { body } = yield call(apiFetch, `i18n/${locale}/languages`, {
            method: 'GET',
        });
        const json = JSON.parse(body);
        yield put(fetchLanguageListSuccessAction(locale, json));
    } catch (error) {
        yield put(
            showNotification('ah.i18n.languages.fetch.failure', 'warning')
        );
    }
}

export default function* coreSagas() {
    yield takeEvery(USER_REGISTER, userRegister);
    yield takeEvery(USER_REGISTER_FAILURE, userRegisterFailure);
    yield takeEvery(USER_REGISTER_SUCCESS, userRegisterSuccess);
    yield takeEvery(
        `${PERSON_EMAIL_SEND_VERIFICATION_EMAIL}_FAILURE`,
        personEmailSendVerificationEmailFailure
    );
    yield takeEvery(
        `${PERSON_EMAIL_SEND_VERIFICATION_EMAIL}_SUCCESS`,
        personEmailSendVerificationEmailSuccess
    );
    yield takeEvery(`${PERSON_EMAIL_VERIFY}_FAILURE`, personEmailVerifyFailure);
    yield takeEvery(`${PERSON_EMAIL_VERIFY}_SUCCESS`, personEmailVerifySuccess);
    yield takeEvery(USER_PASSWORD_RESET_REQUEST, userPasswordResetRequest);
    yield takeEvery(USER_PASSWORD_RESET, userPasswordReset);
    yield takeEvery(CRUD_CREATE_FAILURE, crudCreateFailure);
    yield takeEvery(I18N_COUNTRY_LIST, fetchCountryList);
    yield takeEvery(I18N_LANGUAGE_LIST, fetchLanguageList);
}
