import { combineReducers } from 'redux';

export * from './actions';
export * from './api';
export * from './auth';
export * from './client';
export * from './i18n';
export * from './icons';
export * from './input';
export * from './layout';

import localeReducer from './reducer/locale';
import reducer from './reducer';
import fetchSaga from './sagas/fetch';
import routes from './routes';
import messages from './messages';

export { localeReducer };

export const Bundle = {
    reducer: {
        ah_core: reducer,
    },
    sagas: [fetchSaga],
    routes,
    messages,
};
