import {
    FILE_UPLOAD,
    FILE_UPLOAD_FAILURE,
    FILE_UPLOAD_SUCCESS,
} from './actions';
import { put, takeEvery } from 'redux-saga/effects';
import fileSagas, {
    fileUpload,
    fileUploadFailure,
    fileUploadSuccess,
} from './sagas';
import { showNotification } from 'react-admin';

test('fileSagas', () => {
    const gen = fileSagas();

    expect(gen.next().value).toEqual(takeEvery(FILE_UPLOAD, fileUpload));
    expect(gen.next().value).toEqual(
        takeEvery(FILE_UPLOAD_FAILURE, fileUploadFailure)
    );
    expect(gen.next().value).toEqual(
        takeEvery(FILE_UPLOAD_SUCCESS, fileUploadSuccess)
    );
});

test('fileUploadFailure shows a notification', () => {
    const gen = fileUploadFailure({});
    expect(gen.next().value).toEqual(
        put(showNotification('File upload failed'))
    );
});

test('fileUploadSucess shows a notification', () => {
    const gen = fileUploadSuccess({});
    expect(gen.next().value).toEqual(put(showNotification('File uploaded')));
});
