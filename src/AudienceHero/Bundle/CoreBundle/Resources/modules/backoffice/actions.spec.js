import { ACTION } from './client/hydraClient';
import {
    USER_REGISTER,
    userRegister,
    USER_REGISTER_FAILURE,
    userRegisterFailure,
    USER_REGISTER_SUCCESS,
    userRegisterSuccess,
    PERSON_EMAIL_SEND_VERIFICATION_EMAIL,
    sendPersonEmailVerification,
    PERSON_EMAIL_VERIFY,
    verifyPersonEmail,
    resetUserPasswordRequest,
    USER_PASSWORD_RESET_REQUEST,
} from './actions';
import { CREATE } from 'react-admin';

test('userRegister', () => {
    const result = userRegister({
        username: 'myusername',
        password: 'mypass',
        email: 'myemail',
    });
    expect(result).toEqual({
        type: USER_REGISTER,
        payload: {
            username: 'myusername',
            password: 'mypass',
            email: 'myemail',
        },
    });
});

test('userRegisterFailure', () => {
    const result = userRegisterFailure('value');
    expect(result).toEqual({
        type: USER_REGISTER_FAILURE,
        payload: 'value',
    });
});

test('userRegisterSuccess', () => {
    const result = userRegisterSuccess({
        username: 'myusername',
        password: 'mypassword',
    });
    expect(result).toEqual({
        type: USER_REGISTER_SUCCESS,
        payload: {
            username: 'myusername',
            password: 'mypassword',
        },
    });
});

test('sendPersonEmailVerification', () => {
    const result = sendPersonEmailVerification('myid');
    expect(result).toEqual({
        type: PERSON_EMAIL_SEND_VERIFICATION_EMAIL,
        payload: {
            action: 'send-verification-email',
            id: 'myid',
        },
        meta: {
            resource: 'person_emails',
            fetch: ACTION,
        },
    });
});

test('verifyPersonEmail', () => {
    const result = verifyPersonEmail('myid', 'mytoken');
    expect(result).toEqual({
        type: PERSON_EMAIL_VERIFY,
        payload: {
            action: 'verify',
            id: 'myid',
            data: {
                token: 'mytoken',
            },
        },
        meta: {
            resource: 'person_emails',
            fetch: ACTION,
        },
    });
});

test('resetUserPasswordRequest', () => {
    const result = resetUserPasswordRequest({ email: 'myemail' });
    expect(result).toEqual({
        type: USER_PASSWORD_RESET_REQUEST,
        payload: { data: { email: 'myemail' } },
        meta: {
            resource: 'persons/forgotten-password',
            fetch: CREATE,
        },
    });
});
