import { CONTACT_OPTIN } from './actions';

const defaultState = {
    submitting: false,
};

export default (prevState = defaultState, action) => {
    switch (action.type) {
        case CONTACT_OPTIN:
            return {
                ...prevState,
                submitting: true,
            };
        case `${CONTACT_OPTIN}_FAILURE`:
        case `${CONTACT_OPTIN}_SUCCESS`:
            return {
                ...prevState,
                submitting: false,
            };
    }

    return prevState;
};
