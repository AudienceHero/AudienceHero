import { FETCH_COUNTRIES } from '../actions/i18nActions';

const defaultState = {
    countries: {},
};

export default (previousState = defaultState, { type, payload }) => {
    switch (type) {
        case `${FETCH_COUNTRIES}_SUCCESS`:
            return {
                ...previousState,
                countries: payload,
            };
        default:
            return previousState;
    }
};
