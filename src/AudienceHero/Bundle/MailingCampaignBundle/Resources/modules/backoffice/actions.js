import { ACTION } from '@audiencehero/common';

export const MAILING_SEND = 'AH/MAILING_SEND';
export const sendMailing = id => ({
    type: MAILING_SEND,
    payload: {
        action: 'send',
        id,
        data: {},
    },
    meta: {
        fetch: ACTION,
        resource: 'mailings',
    },
});

export const MAILING_PREVIEW_SEND = 'AH/MAILING_PREVIEW_SEND';
export const sendMailingPreview = ({ id, testRecipient }) => ({
    type: MAILING_PREVIEW_SEND,
    payload: {
        action: 'send-preview',
        id,
        data: { testRecipient },
    },
    meta: {
        fetch: ACTION,
        resource: 'mailings',
    },
});

export const MAILING_BOOST = 'AH/MAILING_BOOST';
export const boostMailing = id => ({
    type: MAILING_SEND,
    payload: {
        action: 'boost',
        id,
        data: {},
    },
    meta: {
        fetch: ACTION,
        resource: 'mailings',
    },
});
