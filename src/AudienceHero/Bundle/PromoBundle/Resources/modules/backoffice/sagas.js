import { takeEvery, all, put, call, take } from 'redux-saga/effects';
import { PROMO_SEND, PROMO_BOOST, PROMO_PREVIEW_SEND } from './actions';

import { showNotification } from 'react-admin';

export function* sendPromoPreviewSuccess() {
    yield put(showNotification('ah.promo.notification.send_preview.success'));
}

export function* sendPromoPreviewFailure() {
    yield put(
        showNotification(
            'ah.promo.notification.send_preview.failure',
            'warning'
        )
    );
}

export function* sendPromoSuccess() {
    yield put(showNotification('ah.promo.notification.send.success'));
}

export function* sendPromoFailure() {
    yield put(
        showNotification('ah.promo.notification.send.failure', 'warning')
    );
}

export function* boostPromoSuccess() {
    yield put(showNotification('ah.promo.notification.boost.success'));
}

export function* boostPromoFailure() {
    yield put(
        showNotification('ah.promo.notification.boost.failure', 'warning')
    );
}

export default function* promoSagas() {
    yield takeEvery(`${PROMO_PREVIEW_SEND}_SUCCESS`, sendPromoPreviewSuccess);
    yield takeEvery(`${PROMO_PREVIEW_SEND}_FAILURE`, sendPromoPreviewFailure);
    yield takeEvery(`${PROMO_SEND}_SUCCESS`, sendPromoSuccess);
    yield takeEvery(`${PROMO_SEND}_FAILURE`, sendPromoFailure);
    yield takeEvery(`${PROMO_BOOST}_SUCCESS`, boostPromoSuccess);
    yield takeEvery(`${PROMO_BOOST}_FAILURE`, boostPromoFailure);
}
