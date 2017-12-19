export * from './actions';
import playerSagas from './sagas/playerSagas';
import reducer from './reducer';
import messages from './messages';

import Player from './Player';

export { Player };

export const Bundle = {
    sagas: [playerSagas],
    reducer: {
        ah_file: reducer,
    },
    messages,
};
