import { takeEvery, put } from 'redux-saga/effects';
import { stopSubmit } from 'redux-form';
import { transformViolations } from '@audiencehero/common';
import { CONTACT_OPTIN } from './actions';
import { push } from 'react-router-redux';

export function* optinFailure({ payload }) {
    yield put(stopSubmit('optin', transformViolations(payload)));
}

export function* optinSuccess({ payload }) {
    let id = payload['@id'];
    id = id.substring(id.lastIndexOf('/') + 1);
    yield put(push(`/forms/${id}/request-confirm`));
}

export default function* watch() {
    yield takeEvery(`${CONTACT_OPTIN}_SUCCESS`, optinSuccess);
    yield takeEvery(`${CONTACT_OPTIN}_FAILURE`, optinFailure);
}
