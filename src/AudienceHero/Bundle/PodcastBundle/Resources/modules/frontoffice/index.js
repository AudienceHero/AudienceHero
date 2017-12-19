import routes from './routes';
import reducer from './reducer';
import messages from './messages';
import sagas from './sagas';

export const Bundle = {
    messages,
    routes,
    sagas,
    reducer: {
        ah_podcast: reducer,
    },
};
