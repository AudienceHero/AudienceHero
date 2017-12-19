import { SET_TITLE } from '../actions/titleActions';

const defaultState = '';

export default (previousState = defaultState, { type, payload }) => {
    switch (type) {
        case SET_TITLE:
            return payload.title;
        default:
            return previousState;
    }
};
