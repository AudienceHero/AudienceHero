import {
    fileUpload,
    fileUploadSuccess,
    fileUploadFailure,
    fileUploadProgress,
    FILE_UPLOAD,
    FILE_UPLOAD_FAILURE,
    FILE_UPLOAD_SUCCESS,
    FILE_UPLOAD_PROGRESS,
} from './actions';

test('fileUpload action', () => {
    expect(fileUpload('fakeFile')).toEqual({
        type: FILE_UPLOAD,
        payload: {
            file: 'fakeFile',
        },
    });
});

test('fileUploadSuccess sends meta to refresh file list', () => {
    expect(fileUploadSuccess('fakeFile')).toEqual({
        type: FILE_UPLOAD_SUCCESS,
        payload: {
            file: 'fakeFile',
        },
        meta: { resource: 'files', fetch: 'GET_LIST' },
    });
});

test('fileUploadFailure', () => {
    expect(fileUploadFailure('fakeFile')).toEqual({
        type: FILE_UPLOAD_FAILURE,
        payload: {
            file: 'fakeFile',
        },
    });
});

test('fileUploadProgress', () => {
    expect(fileUploadProgress('fakeFile', 30)).toEqual({
        type: FILE_UPLOAD_PROGRESS,
        payload: {
            file: 'fakeFile',
            progress: 30,
        },
    });
});
