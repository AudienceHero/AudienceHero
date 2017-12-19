import { OrderedMap } from 'immutable';
import { takeEvery, put, take } from 'redux-saga/effects';
import { eventChannel, END } from 'redux-saga';
import {
    PLAYER_TRACK_LOAD,
    PLAYER_INIT,
    PLAYER_SKIP_NEXT,
    PLAYER_SKIP_PREVIOUS,
    PLAYER_TOGGLE_PLAY_PAUSE,
    PLAYER_TRACK_PLAY,
    PLAYER_TRACK_STOP,
    loadTrack as loadTrackAction,
    stopTrack as stopTrackAction,
    skipNext as skipNextAction,
    updateTrackState as updateTrackStateAction,
    trackHasEnded as trackHasEndedAction,
    PLAYER_TRACK_SKIP,
    PLAYER_TRACK_UPDATE_STATE,
    PLAYER_SILENCE,
} from '../actions/playerActions';
import { recordActivity } from '@audiencehero-frontoffice/activity';

const SoundManager = require('soundmanager2').soundManager;

const pendingCalls = [];

SoundManager.setup({
    debugFlash: false,
    preferFlash: false,
    debugMode: true,
    onready: function() {
        pendingCalls.slice().forEach(cb => cb());
    },
});

var playlist = new OrderedMap();

const trackID = id => id;

export const createSound = (SoundManager, options) => {
    if (SoundManager.ok()) {
        const id = options.id;
        const sound = SoundManager.getSoundById(id);
        if (typeof sound != 'undefined' && sound != null) {
            sound.play();
        } else {
            SoundManager.createSound(options);
        }
        return () => {};
    } else {
        const call = () => {
            SoundManager.createSound(options);
        };
        pendingCalls.push(call);

        return () => {
            pendingCalls.splice(pendingCalls.indexOf(call), 1);
        };
    }
};

export function* togglePlayPause({ payload: { currentTrack } }) {
    SoundManager.togglePause(currentTrack['@id']);
}

export function* playerStop({ payload: { id } }) {
    SoundManager.stop(trackID(id));
}

export function* playerTrackSkip({ payload: { id, skipPosition } }) {
    SoundManager.getSoundById(trackID(id)).setPosition(skipPosition);
}

export function* playerSilence() {
    SoundManager.pauseAll();
}

export function* init({ payload: { player, autoplay } }) {
    for (var track of player.tracks) {
        playlist = playlist.set(trackID(track['@id']), track);
    }

    // Disable autoplay on mobile
    if (
        /(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|ipad|iris|kindle|Android|Silk|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i.test(
            navigator.userAgent
        ) ||
        /1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(
            navigator.userAgent.substr(0, 4)
        )
    ) {
        autoplay = false;
    }

    // load first track
    yield put(loadTrackAction({ track: playlist.first(), autoplay, playlist }));
}

export function* loadTrack({ payload: { track, autoplay, playlist } }) {
    const sound = {
        id: trackID(track['@id']),
        url: track.file.remoteUrl,
        autoLoad: autoplay,
        autoPlay: autoplay,
    };

    const channel = eventChannel(emitter => {
        sound.onplay = function() {
            emitter({ event: 'onplay', sound: this });
        };
        sound.onpause = function() {
            emitter({ event: 'onpause', sound: this });
        };
        sound.whileloading = function() {
            emitter({ event: 'whileloading', sound: this });
        };
        sound.whileplaying = function() {
            emitter({ event: 'whileplaying', sound: this });
        };
        sound.onfinish = function() {
            emitter({ event: 'onfinish', sound: this });
        };

        return () => {
            emitter(END);
        };
    });

    createSound(SoundManager, sound);

    while (true) {
        const data = yield take(channel);
        if (!data) {
            break;
        }

        switch (data.event) {
            case 'onplay':
            case 'onpause':
            case 'whileloading':
            case 'whileplaying':
                yield put(
                    updateTrackStateAction({
                        id: trackID(track['@id']),
                        track,
                        sound: data.sound,
                    })
                );
                break;
            case 'onfinish':
                yield put(
                    updateTrackStateAction({
                        id: trackID(track['@id']),
                        track,
                        sound: data.sound,
                    })
                );
                yield put(trackHasEndedAction({ track }));
                yield put(skipNextAction({ playlist, currentTrack: track }));
                break;
        }
    }
}

export function* skipPrevious({ payload: { currentTrack, playlist } }) {
    // Abort if we are at the beginning of the playlist
    if (currentTrack.position === 0) {
        return;
    }

    const prevTrack = playlist.find(value => {
        return value.position === currentTrack.position - 1;
    });

    if (typeof prevTrack === 'undefined') {
        return;
    }

    yield put(stopTrackAction(currentTrack));
    yield put(loadTrackAction({ track: prevTrack, playlist, autoplay: true }));
}

export function* skipNext({ payload: { currentTrack, playlist } }) {
    // Abort if we are at the end of the playlist
    if (currentTrack.position + 1 === playlist.size) {
        return;
    }

    const nextTrack = playlist.find(value => {
        return value.position === currentTrack.position + 1;
    });

    if (typeof nextTrack === 'undefined') {
        return;
    }

    yield put(stopTrackAction(currentTrack));
    yield put(loadTrackAction({ track: nextTrack, playlist, autoplay: true }));
}

export function* stopTrack({ payload: { track } }) {
    SoundManager.stop(track['@id']);
}

export function* playTrack({ payload: { track, currentTrack, playlist } }) {
    yield put(stopTrackAction(currentTrack));
    yield put(loadTrackAction({ track: track, playlist, autoplay: true }));
}

export function* skipTrack({ payload: { track, position } }) {
    const sound = SoundManager.getSoundById(track['@id']);
    if (typeof sound === 'undefined') {
        return;
    }
    sound.setPosition(position);
}

const progressTracker = [];
export function* recordTrackProgress({ payload: { track, sound } }) {
    const id = track.file['@id'];
    const playProgress = sound.position / sound.durationEstimate * 100;

    const previousStatus = progressTracker[id];
    if (typeof previousStatus === 'undefined') {
        yield put(
            recordActivity({ type: 'file.play.started', subjects: [id] })
        );
        progressTracker[id] = {
            started: true,
            partial: false,
            complete: false,
        };

        return;
    }

    // As this point, we already tracked the progress of the current track.
    if (playProgress > 10 && playProgress <= 90 && !previousStatus.partial) {
        yield put(
            recordActivity({ type: 'file.play.partial', subjects: [id] })
        );
        progressTracker[id] = { started: true, partial: true, complete: false };

        return;
    }
    if (playProgress > 90 && !previousStatus.complete) {
        yield put(
            recordActivity({ type: 'file.play.complete', subjects: [id] })
        );
        progressTracker[id] = { started: true, partial: true, complete: true };

        return;
    }
}

export function* playerSaga() {
    yield takeEvery(PLAYER_INIT, init);
    yield takeEvery(PLAYER_TOGGLE_PLAY_PAUSE, togglePlayPause);
    yield takeEvery(PLAYER_SKIP_PREVIOUS, skipPrevious);
    yield takeEvery(PLAYER_SKIP_NEXT, skipNext);
    yield takeEvery(PLAYER_TRACK_LOAD, loadTrack);
    yield takeEvery(PLAYER_TRACK_PLAY, playTrack);
    yield takeEvery(PLAYER_TRACK_STOP, stopTrack);
    yield takeEvery(PLAYER_TRACK_SKIP, skipTrack);
    yield takeEvery(PLAYER_TRACK_UPDATE_STATE, recordTrackProgress);
    yield takeEvery(PLAYER_SILENCE, playerSilence);
}

export default playerSaga;
