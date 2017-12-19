import {
    PLAYER_INIT,
    PLAYER_TRACK_LOAD,
    PLAYER_TRACK_ENDED,
    PLAYER_TRACK_UPDATE_STATE,
} from '../actions/playerActions';
import { Map, OrderedMap } from 'immutable';

const defaultState = {
    volume: 100,

    // SoundManager sounds
    loadedSounds: new Map(),
    currentlyLoaded: null,
    currentlyLoadedFinished: false,
    playlist: new OrderedMap(),
};

export const playerReducer = (state = defaultState, action) => {
    switch (action.type) {
        case PLAYER_TRACK_LOAD:
            return {
                ...state,
                currentlyLoaded: action.payload.track,
                currentlyLoadedFinished: false,
                playlist: action.payload.playlist,
            };
        case PLAYER_TRACK_UPDATE_STATE:
            return {
                ...state,
                loadedSounds: state.loadedSounds.set(
                    action.payload.id,
                    action.payload.sound
                ),
            };
        default:
            return state;
    }
};

export default playerReducer;
