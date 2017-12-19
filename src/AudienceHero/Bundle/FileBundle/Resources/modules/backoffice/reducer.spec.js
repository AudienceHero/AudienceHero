import {
    fileUpload,
    fileUploadProgress,
    fileUploadSuccess,
    fileUploadFailure,
} from './actions';
import { file as reducer } from './reducer';

const fakeFile = { name: 'Foobar.jpg' };

test('reducer sets correct state upon file upload', () => {
    const state = reducer(undefined, fileUpload(fakeFile));

    expect(state.getIn(['uploads', 'Foobar.jpg'])).toEqual({ file: fakeFile });
});

test('reducer sets correct state upon file upload', () => {
    const state = reducer(undefined, fileUploadProgress(fakeFile, 40));

    expect(state.getIn(['uploads', 'Foobar.jpg'])).toEqual({
        file: fakeFile,
        progress: 40,
    });
});

test('reducer removes files upon success', () => {
    let state = reducer(undefined, fileUpload(fakeFile));
    expect(state.getIn(['uploads', 'Foobar.jpg'])).toEqual({ file: fakeFile });

    state = reducer(state, fileUploadSuccess(fakeFile));
    expect(state.getIn(['uploads', 'Foobar.jpg'])).toEqual(undefined);
});

test('reducer removes files upon success', () => {
    let state = reducer(undefined, fileUpload(fakeFile));
    expect(state.getIn(['uploads', 'Foobar.jpg'])).toEqual({ file: fakeFile });

    state = reducer(state, fileUploadFailure(fakeFile));
    expect(state.getIn(['uploads', 'Foobar.jpg'])).toEqual(undefined);
});
