/*
 * Taken from Admin-On-Rest.
 * Licensed under the MIT license.
 *
 * Copyright (c) 2016-present, Francois Zaninotto, Marmelab
 * Copyright (c) 2017-present, Marc Weistroff
 */
import {
    all,
    put,
    call,
    cancelled,
    takeEvery,
    takeLatest,
} from 'redux-saga/effects';
import {
    FETCH_START,
    FETCH_END,
    FETCH_CANCEL,
    FETCH_ERROR,
} from '../actions/fetchActions';
import apiFetch from '../api/fetch';
import { push } from 'react-router-redux';

function* handleFetch(action) {
    const { type, payload, meta: { fetch: fetchMeta, ...meta } } = action;
    const restType = fetchMeta;

    yield all([
        put({ type: `${type}_LOADING`, payload, meta }),
        put({ type: FETCH_START }),
    ]);
    let response;
    var path = `${meta.resource}`;
    if (meta.id) {
        path = `${path}/${meta.id}`;
    }
    if (meta.action) {
        path = `${path}/${meta.action}`;
    }

    try {
        response = yield call(
            apiFetch,
            path,
            meta.auth ? meta.auth : false,
            payload.data ? payload.data : null,
            meta.options
        );
        yield put({
            type: `${type}_SUCCESS`,
            payload: response.json,
            requestPayload: payload,
            meta: {
                ...meta,
                fetchStatus: FETCH_END,
            },
        });
        yield put({ type: FETCH_END });
    } catch (error) {
        yield put({
            type: `${type}_FAILURE`,
            error: error.message ? error.message : error,
            payload: error.body ? error.body : null,
            requestPayload: payload,
            meta: {
                ...meta,
                fetchResponse: restType,
                fetchStatus: FETCH_ERROR,
            },
        });
        yield put({ type: FETCH_ERROR, error });
        switch (error.status) {
            // TODO: Add other status code (404, 401, 500?)
            case 403:
                yield put(push('/403'));
                break;
            default:
                break;
        }
    } finally {
        if (yield cancelled()) {
            yield put({ type: FETCH_CANCEL });
            return; /* eslint no-unsafe-finally:0 */
        }
    }
}

export default function* watchFetch() {
    yield all([
        takeEvery(action => action.meta && action.meta.fetch, handleFetch),
    ]);
}
