import { FETCH_DATA } from '@audiencehero-frontoffice/core';

export const PROMO_SUBMIT_FEEDBACK = 'AH/PROMO_SUBMIT_FEEDBACK';
export const submitPromoFeedback = ({ promoId, id, recipientId, values }) => ({
    type: PROMO_SUBMIT_FEEDBACK,
    payload: {
        data: {
            ...values,
            rating: parseInt(values.rating),
            promo: promoId,
            recipientId: recipientId,
        },
    },
    meta: {
        resource: 'promos',
        id,
        fetch: FETCH_DATA,
        action: 'feedback',
        auth: recipientId === 'preview' ? true : false,
        options: {
            method: 'POST',
        },
    },
});
