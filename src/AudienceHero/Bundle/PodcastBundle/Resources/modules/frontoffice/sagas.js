import { takeEvery, put } from 'redux-saga/effects';
import { FETCH_DATA } from '@audiencehero-frontoffice/core';
import { recordActivity } from '@audiencehero-frontoffice/activity';

export function* recordChannelHit({ meta: { id } }) {
    yield put(
        recordActivity({
            type: 'podcast_channel.hit',
            subjects: [`/api/podcast_channels/${id}`],
        })
    );
}

export function* recordEpisodeHit({ meta: { id } }) {
    yield put(
        recordActivity({
            type: 'podcast_episode.hit',
            subjects: [`/api/podcast_episodes/${id}`],
        })
    );
}

export default function* watch() {
    yield takeEvery(
        action =>
            action.type === FETCH_DATA &&
            action.meta.fetch === FETCH_DATA &&
            action.meta.resource == 'podcast_channels' &&
            !action.meta.auth,
        recordChannelHit
    );
    yield takeEvery(
        action =>
            action.type === FETCH_DATA &&
            action.meta.fetch === FETCH_DATA &&
            action.meta.resource == 'podcast_episodes' &&
            !action.meta.auth,
        recordEpisodeHit
    );
}
