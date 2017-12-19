import routes from './routes';
import reducer from './reducer';
import messages from './messages';
import sagas from './sagas';

const Bundle = {
    routes,
    reducer: {
        ah_afd: reducer,
    },
    messages,
    sagas,
};

export { Bundle };
