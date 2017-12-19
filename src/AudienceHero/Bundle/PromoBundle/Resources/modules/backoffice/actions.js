import { ACTION } from '@audiencehero/common';

export const PROMO_SEND = 'AH/PROMO_SEND';
export const sendPromo = id => ({
    type: PROMO_SEND,
    payload: {
        action: 'send',
        id,
        data: {},
    },
    meta: {
        fetch: ACTION,
        resource: 'promos',
    },
});

export const PROMO_BOOST = 'AH/PROMO_BOOST';
export const boostPromo = id => ({
    type: PROMO_BOOST,
    payload: {
        action: 'boost',
        id,
        data: {},
    },
    meta: {
        fetch: ACTION,
        resource: 'promos',
    },
});

export const PROMO_PREVIEW_SEND = 'AH/PROMO_PREVIEW_SEND';
export const sendPromoPreview = ({ id, testRecipient }) => ({
    type: PROMO_PREVIEW_SEND,
    payload: {
        action: 'send-preview',
        id,
        data: {
            testRecipient,
        },
    },
    meta: {
        fetch: ACTION,
        resource: 'promos',
    },
});
