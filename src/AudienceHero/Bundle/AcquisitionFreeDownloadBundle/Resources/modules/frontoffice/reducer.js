import { ACQUISITION_FREE_DOWNLOAD_UNLOCK } from './actions';

const defaultState = {
    unlocking: false,
    downloadUrl: null,
};

export default (prevState = defaultState, action) => {
    switch (action.type) {
        case ACQUISITION_FREE_DOWNLOAD_UNLOCK:
            return {
                ...prevState,
                unlocking: true,
            };
        case `${ACQUISITION_FREE_DOWNLOAD_UNLOCK}_FAILURE`:
            return {
                ...prevState,
                unlocking: false,
            };
        case `${ACQUISITION_FREE_DOWNLOAD_UNLOCK}_SUCCESS`:
            return {
                ...prevState,
                unlocking: false,
                downloadUrl: action.payload.url,
            };
    }

    return prevState;
};
