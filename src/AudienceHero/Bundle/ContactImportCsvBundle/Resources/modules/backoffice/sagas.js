import { takeEvery, all, put, call, take } from 'redux-saga/effects';
import { buffers, eventChannel, END } from 'redux-saga';
import { push } from 'react-router-redux';
import { stopSubmit } from 'redux-form';

import { showNotification } from 'react-admin';
import { CSV_FILE_UPLOAD, CSV_FILE_IMPORT } from './actions';

import { transformViolations } from '@audiencehero/common';

export function* uploadCsvFileSuccess({ payload }) {
    const id = payload.data.id;
    yield put(push(`/contacts/import/csv/${id}`));
}

export function* uploadCsvFileFailure(action) {
    yield put(
        showNotification('ah.csv_import.notification.upload.failure', 'warning')
    );
}

export function* importCsvFileSuccess(action) {
    yield put(push('/import'));
    yield put(showNotification('ah.csv_import.notification.import.success'));
}

export function* importCsvFileFailure(action) {
    yield put(
        stopSubmit('csv-import-form', transformViolations(action.payload))
    );
    yield put(
        showNotification('ah.csv_import.notification.import.failure', 'warning')
    );
}

export default function* csvSagas() {
    yield takeEvery(`${CSV_FILE_UPLOAD}_FAILURE`, uploadCsvFileFailure);
    yield takeEvery(`${CSV_FILE_UPLOAD}_SUCCESS`, uploadCsvFileSuccess);
    yield takeEvery(`${CSV_FILE_IMPORT}_FAILURE`, importCsvFileFailure);
    yield takeEvery(`${CSV_FILE_IMPORT}_SUCCESS`, importCsvFileSuccess);
}
