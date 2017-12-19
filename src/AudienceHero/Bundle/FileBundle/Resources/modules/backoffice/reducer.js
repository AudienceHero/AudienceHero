import {
    FILE_UPLOAD,
    FILE_UPLOAD_PROGRESS,
    FILE_UPLOAD_FAILURE,
    FILE_UPLOAD_SUCCESS,
} from './actions';
import { Map } from 'immutable';

export const file = (state = Map({ uploads: Map() }), { type, payload }) => {
    switch (type) {
        case FILE_UPLOAD:
            return state.setIn(['uploads', payload.file.name], {
                file: payload.file,
            });
        case FILE_UPLOAD_PROGRESS:
            return state.setIn(['uploads', payload.file.name], {
                file: payload.file,
                progress: payload.progress,
            });
        case FILE_UPLOAD_FAILURE:
        case FILE_UPLOAD_SUCCESS:
            return state.deleteIn(['uploads', payload.file.name]);
        default:
            return state;
    }
};

export default file;
