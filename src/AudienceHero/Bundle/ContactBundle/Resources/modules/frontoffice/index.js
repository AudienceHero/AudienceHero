export * from './input';
export * from './Optin';

import messages from './messages';
import routes from './routes';
import sagas from './sagas';
import reducer from './reducer';

const Bundle = {
    messages,
    routes,
    sagas,
    reducer: {
        ah_contact: reducer,
    },
};

export { Bundle };
