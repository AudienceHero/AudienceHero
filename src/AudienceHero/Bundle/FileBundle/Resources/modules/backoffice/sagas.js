import { takeEvery, all, put, call, take } from 'redux-saga/effects';
import { buffers, eventChannel, END } from 'redux-saga';

import { showNotification } from 'react-admin';
import {
    FILE_UPLOAD,
    FILE_UPLOAD_SUCCESS,
    FILE_UPLOAD_FAILURE,
    fileUploadFailure as fileUploadFailureAction,
    fileUploadSuccess as fileUploadSuccessAction,
    fileUploadProgress as fileUploadProgressAction,
} from './actions';

// TODO: Find a way to inject entrypoint
const entrypoint = '';

function createUploadFileChannel(file) {
    return eventChannel(emitter => {
        const xhr = new XMLHttpRequest();

        const onProgress = progressEvent => {
            if (progressEvent.lengthComputable) {
                const progress = progressEvent.loaded / progressEvent.total;
                emitter({ progress });
            }
        };

        const onFailure = progressEvent => {
            emitter({ err: new Error('Upload failed') });
            emitter(END);
        };

        xhr.withCredentials = false;
        xhr.upload.addEventListener('progress', onProgress);
        xhr.upload.addEventListener('error', onFailure);
        xhr.upload.addEventListener('abort', onFailure);
        xhr.onreadystatechange = e => {
            const { readyState, status } = xhr;
            if (readyState === 4) {
                if (status === 200) {
                    emitter({ success: true });
                    emitter(END);
                } else {
                    onFailure(null);
                }
            }
        };

        xhr.open('POST', entrypoint + '/api/upload');
        xhr.setRequestHeader('Accept', 'application/ld+json');
        xhr.setRequestHeader(
            'Authorization',
            `Bearer ${localStorage.getItem('token')}`
        );
        const formData = new FormData();
        formData.append('file', file);
        xhr.send(formData);

        return () => {
            xhr.upload.removeEventListener('progress', onProgress);
            xhr.upload.removeEventListener('error', onFailure);
            xhr.upload.removeEventListener('abort', onFailure);
            xhr.onreadystatechange = null;
            xhr.abort();
        };
    }, buffers.sliding(2));
}

export function* fileUpload(action) {
    const file = action.payload.file;
    const channel = yield call(createUploadFileChannel, file);
    while (true) {
        const { err, progress, success } = yield take(channel);
        if (err) {
            yield put(fileUploadFailureAction(file));
            return;
        }
        if (success) {
            yield put(fileUploadSuccessAction(file));
            return;
        }

        yield put(fileUploadProgressAction(file, progress));
    }
}

export function* fileUploadSuccess(action) {
    yield put(showNotification('File uploaded'));
}

export function* fileUploadFailure(action) {
    yield put(showNotification('File upload failed'));
}

export default function* fileSagas() {
    yield takeEvery(FILE_UPLOAD, fileUpload);
    yield takeEvery(FILE_UPLOAD_FAILURE, fileUploadFailure);
    yield takeEvery(FILE_UPLOAD_SUCCESS, fileUploadSuccess);
}
