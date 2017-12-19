import { takeEvery, all, put, call, take } from 'redux-saga/effects';
import { apiFetch } from './api/fetch';
import { push } from 'react-router-redux';
import {
    CRUD_CREATE_FAILURE,
    userLogin as userLoginAction,
    showNotification,
} from 'react-admin';
import { stopSubmit } from 'redux-form';
import {
    userRegisterFailure as userRegisterFailureAction,
    userRegisterSuccess as userRegisterSuccessAction,
    USER_REGISTER,
    USER_REGISTER_FAILURE,
    USER_REGISTER_SUCCESS,
    PERSON_EMAIL_SEND_VERIFICATION_EMAIL,
    PERSON_EMAIL_VERIFY,
    USER_PASSWORD_RESET_REQUEST,
} from './actions';
import coreSagas, {
    crudCreateFailure,
    userRegister,
    userRegisterFailure,
    userRegisterSuccess,
    personEmailSendVerificationEmailSuccess,
    personEmailSendVerificationEmailFailure,
    personEmailVerifyFailure,
    personEmailVerifySuccess,
    userPasswordResetFailure,
    userPasswordResetSuccess,
} from './sagas';
import { violationsResponse } from './client/violations.spec';
import { transformViolations } from './client/violations';

test('coreSagas', () => {
    const generator = coreSagas();

    expect(generator.next().value).toEqual(
        takeEvery(USER_REGISTER, userRegister)
    );
    expect(generator.next().value).toEqual(
        takeEvery(USER_REGISTER_FAILURE, userRegisterFailure)
    );
    expect(generator.next().value).toEqual(
        takeEvery(USER_REGISTER_SUCCESS, userRegisterSuccess)
    );
    expect(generator.next().value).toEqual(
        takeEvery(
            `${PERSON_EMAIL_SEND_VERIFICATION_EMAIL}_FAILURE`,
            personEmailSendVerificationEmailFailure
        )
    );
    expect(generator.next().value).toEqual(
        takeEvery(
            `${PERSON_EMAIL_SEND_VERIFICATION_EMAIL}_SUCCESS`,
            personEmailSendVerificationEmailSuccess
        )
    );
    expect(generator.next().value).toEqual(
        takeEvery(`${PERSON_EMAIL_VERIFY}_FAILURE`, personEmailVerifyFailure)
    );
    expect(generator.next().value).toEqual(
        takeEvery(`${PERSON_EMAIL_VERIFY}_SUCCESS`, personEmailVerifySuccess)
    );
    expect(generator.next().value).toEqual(
        takeEvery(
            `${USER_PASSWORD_RESET_REQUEST}_FAILURE`,
            userPasswordResetFailure
        )
    );
    expect(generator.next().value).toEqual(
        takeEvery(
            `${USER_PASSWORD_RESET_REQUEST}_SUCCESS`,
            userPasswordResetSuccess
        )
    );
    expect(generator.next().value).toEqual(
        takeEvery(CRUD_CREATE_FAILURE, crudCreateFailure)
    );
});

test('personEmailVerifyFailure', () => {
    const generator = personEmailVerifyFailure();

    expect(generator.next().value).toEqual(
        put(
            showNotification(
                'ah.person_email.notification.verification.failure',
                'warning'
            )
        )
    );
    expect(generator.next().value).toEqual(put(push('/person_emails')));
});

test('personEmailVerifySuccess', () => {
    const generator = personEmailVerifySuccess();

    expect(generator.next().value).toEqual(
        put(
            showNotification(
                'ah.person_email.notification.verification.success'
            )
        )
    );
    expect(generator.next().value).toEqual(put(push('/')));
});

test('personEmailSendVerificationEmailFailure', () => {
    const generator = personEmailSendVerificationEmailFailure();

    expect(generator.next().value).toEqual(
        put(
            showNotification(
                'ah.person_email.notification.verification_email_sent.failure',
                'warning'
            )
        )
    );
});

test('personEmailSendVerificationEmailSuccess', () => {
    const generator = personEmailSendVerificationEmailSuccess();

    expect(generator.next().value).toEqual(
        put(
            showNotification(
                'ah.person_email.notification.verification_email_sent.success'
            )
        )
    );
});

test('userRegisterFailure', () => {
    const payload = JSON.parse(violationsResponse);
    const generator = userRegisterFailure({ payload });

    expect(generator.next().value).toEqual(
        put(showNotification('ah.user.registration.failure', 'warning'))
    );
    expect(generator.next().value).toEqual(
        put(stopSubmit('register', transformViolations(payload)))
    );
});

test('userRegisterSuccess', () => {
    const payload = { username: 'myuser', password: 'mypass' };
    const generator = userRegisterSuccess({ payload });

    expect(generator.next().value).toEqual(
        put(showNotification('ah.user.registration.success'))
    );
    expect(generator.next().value).toEqual(put(userLoginAction(payload)));
});

test('userPasswordResetSuccess', () => {
    const generator = userPasswordResetSuccess();

    expect(generator.next().value).toEqual(
        put(showNotification('ah.user.notification.password_reset.success'))
    );
});

test('userPasswordResetFailure', () => {
    const generator = userPasswordResetFailure();

    expect(generator.next().value).toEqual(
        put(
            showNotification(
                'ah.user.notification.password_reset.failure',
                'warning'
            )
        )
    );
});
