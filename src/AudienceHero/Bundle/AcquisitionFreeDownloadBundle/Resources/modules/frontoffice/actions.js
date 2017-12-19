import { FETCH_DATA } from '@audiencehero-frontoffice/core';

export const ACQUISITION_FREE_DOWNLOAD_UNLOCK = 'AH/AFD_UNLOCK';
export const unlockDownload = ({ id, values, auth }) => ({
    type: ACQUISITION_FREE_DOWNLOAD_UNLOCK,
    payload: {
        data: values,
    },
    meta: {
        resource: 'acquisition_free_downloads',
        id,
        fetch: FETCH_DATA,
        action: 'unlock',
        auth,
        options: {
            method: 'PUT',
        },
    },
});
