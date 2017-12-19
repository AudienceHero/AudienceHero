import { CREATE, GET_ONE } from 'react-admin';
import { ACTION } from '@audiencehero/common';

export const CSV_FILE_UPLOAD = 'AH/CONTACTS_IMPORT_CSV_FILE_UPLOAD';
export const uploadCsvFile = ({ text, group }) => ({
    type: CSV_FILE_UPLOAD,
    payload: {
        data: {
            documentType: 'csv.contacts',
            contentType: 'text/csv',
            text,
            subjects: [group],
        },
    },
    meta: {
        fetch: CREATE,
        resource: 'text_stores',
    },
});

export const CSV_FILE_IMPORT = 'AH/CONTACTS_IMPORT_CSV_FILE_IMPORT';
export const importCsvContacts = ({ id, metadata }) => ({
    type: CSV_FILE_IMPORT,
    payload: {
        id,
        action: 'import',
        data: {
            metadata,
        },
    },
    meta: {
        fetch: ACTION,
        resource: 'text_stores',
    },
});
