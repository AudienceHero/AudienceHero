export const FILE_UPLOAD = 'FILE_UPLOAD';
export const fileUpload = file => ({
    type: FILE_UPLOAD,
    payload: { file },
});

export const FILE_UPLOAD_SUCCESS = 'FILE_UPLOAD_SUCCESS';
export const fileUploadSuccess = file => ({
    type: FILE_UPLOAD_SUCCESS,
    payload: { file },
    meta: { resource: 'files', fetch: 'GET_LIST' },
});

export const FILE_UPLOAD_FAILURE = 'FILE_UPLOAD_FAILURE';
export const fileUploadFailure = file => ({
    type: FILE_UPLOAD_FAILURE,
    payload: { file },
});

export const FILE_UPLOAD_PROGRESS = 'FILE_UPLOAD_PROGRESS';
export const fileUploadProgress = (file, progress) => ({
    type: FILE_UPLOAD_PROGRESS,
    payload: { file, progress },
});
