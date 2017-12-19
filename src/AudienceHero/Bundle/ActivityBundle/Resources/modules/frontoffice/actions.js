import { FETCH_DATA } from '@audiencehero-frontoffice/core';

export const ACTIVITY_RECORD = 'AH/ACTIVITY_RECORD';
export const recordActivity = ({ type, subjects }) => ({
    type: ACTIVITY_RECORD,
    payload: {
        data: {
            type,
            subjects,
        },
    },
    meta: {
        resource: 'activities',
        fetch: FETCH_DATA,
        auth: false,
        options: {
            method: 'POST',
        },
    },
});
