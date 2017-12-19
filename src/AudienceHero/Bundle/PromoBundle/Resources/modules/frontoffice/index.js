import routes from './routes';
import reducer from './reducer';
import messages from './messages';

export const Bundle = {
    routes,
    reducer: {
        ah_promo: reducer,
    },
    messages,
};
