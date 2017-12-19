import { PROMO_SUBMIT_FEEDBACK } from './actions';

const defaultState = {
    submittingFeedback: false,
    downloadUrl: null,
};

export const reducer = (prevState = defaultState, action) => {
    switch (action.type) {
        case PROMO_SUBMIT_FEEDBACK:
            return {
                ...prevState,
                submittingFeedback: true,
            };
        case `${PROMO_SUBMIT_FEEDBACK}_SUCCESS`:
            return {
                ...prevState,
                submittingFeedback: false,
                downloadUrl: action.payload.url,
            };
        case `${PROMO_SUBMIT_FEEDBACK}_FAILURE`:
            return {
                ...prevState,
                submittingFeedback: false,
                downloadUrl: null,
            };
        default:
            return prevState;
    }
};

export default reducer;
