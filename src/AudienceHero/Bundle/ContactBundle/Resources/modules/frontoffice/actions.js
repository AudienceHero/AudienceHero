import { FETCH_DATA } from '@audiencehero-frontoffice/core';

export const CONTACT_OPTIN = 'AH/CONTACT_OPTIN';
export const optin = ({ id, values }) => ({
    type: CONTACT_OPTIN,
    payload: {
        data: values,
    },
    meta: {
        resource: 'contacts_group_forms',
        id,
        fetch: FETCH_DATA,
        action: 'optin',
        auth: false,
        options: {
            method: 'POST',
        },
    },
});
