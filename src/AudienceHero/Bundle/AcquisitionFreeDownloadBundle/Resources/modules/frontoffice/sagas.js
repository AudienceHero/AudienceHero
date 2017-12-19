import { takeEvery, put } from 'redux-saga/effects';
import { stopSubmit } from 'redux-form';
import { transformViolations } from '@audiencehero/common';
import { ACQUISITION_FREE_DOWNLOAD_UNLOCK } from './actions';
import { FETCH_DATA } from '@audiencehero-frontoffice/core';
import { recordActivity } from '@audiencehero-frontoffice/activity';

export function* unlockFailure({ payload }) {
    yield put(stopSubmit('afd-unlock', transformViolations(payload)));
}

export function* recordHit({ meta: { id } }) {
    yield put(
        recordActivity({
            type: 'acquisition_free_download.hit',
            subjects: [`/api/acquisition_free_downloads/${id}`],
        })
    );
}

export default function* watch() {
    yield takeEvery(
        `${ACQUISITION_FREE_DOWNLOAD_UNLOCK}_FAILURE`,
        unlockFailure
    );
    yield takeEvery(
        action =>
            action.type === FETCH_DATA &&
            action.meta.fetch === FETCH_DATA &&
            action.meta.resource == 'acquisition_free_downloads' &&
            !action.meta.auth,
        recordHit
    );
}
