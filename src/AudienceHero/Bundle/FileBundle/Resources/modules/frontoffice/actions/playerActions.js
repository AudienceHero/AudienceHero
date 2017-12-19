export const PLAYER_INIT = 'AH/PLAYER_INIT';
export const initPlayer = (player, autoplay) => ({
    type: PLAYER_INIT,
    payload: {
        player,
        autoplay,
    },
});

export const PLAYER_SILENCE = 'AH/PLAYER_SILENCE';
export const silencePlayer = () => ({
    type: PLAYER_SILENCE,
    payload: {},
});

export const PLAYER_TOGGLE_PLAY_PAUSE = 'AH/PLAYER_TOGGLE_PLAY_PAUSE';
export const togglePlayPause = ({ currentTrack }) => ({
    type: PLAYER_TOGGLE_PLAY_PAUSE,
    payload: {
        currentTrack,
    },
});

export const PLAYER_SKIP_NEXT = 'AH/PLAYER_SKIP_NEXT';
export const skipNext = ({ currentTrack, playlist }) => ({
    type: PLAYER_SKIP_NEXT,
    payload: { currentTrack, playlist },
});

export const PLAYER_SKIP_PREVIOUS = 'AH/PLAYER_SKIP_PREVIOUS';
export const skipPrevious = ({ currentTrack, playlist }) => ({
    type: PLAYER_SKIP_PREVIOUS,
    payload: { currentTrack, playlist },
});

export const PLAYER_TRACK_LOAD = 'AH/PLAYER_TRACK_LOAD';
export const loadTrack = ({ track, playlist, autoplay }) => ({
    type: PLAYER_TRACK_LOAD,
    payload: {
        track,
        playlist,
        autoplay,
    },
});

export const PLAYER_TRACK_PLAY = 'AH/PLAYER_TRACK_PLAY';
export const playTrack = ({ track, currentTrack, playlist }) => ({
    type: PLAYER_TRACK_PLAY,
    payload: { currentTrack, track, playlist },
});

export const PLAYER_TRACK_STOP = 'AH/PLAYER_TRACK_STOP';
export const stopTrack = track => ({
    type: PLAYER_TRACK_STOP,
    payload: { track },
});

export const PLAYER_TRACK_UPDATE_STATE = 'AH/PLAYER_TRACK_UPDATE_STATE';
export const updateTrackState = ({ id, track, sound }) => ({
    type: PLAYER_TRACK_UPDATE_STATE,
    payload: {
        id,
        sound,
        track,
    },
});

export const PLAYER_TRACK_ENDED = 'AH/PLAYER_TRACK_ENDED';
export const trackHasEnded = track => ({
    type: PLAYER_TRACK_ENDED,
    payload: { track },
});

export const PLAYER_TRACK_SKIP = 'AH/PLAYER_TRACK_SKIP';
export const trackSkip = ({ track, position }) => ({
    type: PLAYER_TRACK_SKIP,
    payload: { track, position },
});
