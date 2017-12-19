import { takeEvery, all, put, call, take } from 'redux-saga/effects';
import { MAILING_SEND, MAILING_PREVIEW_SEND } from './actions';

import { showNotification } from 'react-admin';

export function* sendMailingPreviewSuccess() {
    yield put(showNotification('ah.mailing.notification.send_preview.success'));
}

export function* sendMailingPreviewFailure() {
    yield put(
        showNotification(
            'ah.mailing.notification.send_preview.failure',
            'warning'
        )
    );
}

export function* sendMailingSuccess() {
    yield put(showNotification('ah.mailing.notification.send.success'));
}

export function* sendMailingFailure() {
    yield put(
        showNotification('ah.mailing.notification.send.failure', 'warning')
    );
}

export default function* mailingSagas() {
    yield takeEvery(
        `${MAILING_PREVIEW_SEND}_SUCCESS`,
        sendMailingPreviewSuccess
    );
    yield takeEvery(
        `${MAILING_PREVIEW_SEND}_FAILURE`,
        sendMailingPreviewFailure
    );
    yield takeEvery(`${MAILING_SEND}_SUCCESS`, sendMailingSuccess);
    yield takeEvery(`${MAILING_SEND}_FAILURE`, sendMailingFailure);
}
