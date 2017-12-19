import React from 'react';
import { Route } from 'react-router';
import PodcastChannel from './PodcastChannel';
import PodcastEpisode from './PodcastEpisode';

const routes = [
    <Route exact path="/podcasts/:id" component={PodcastChannel} />,
    <Route exact path="/podcasts/:id/:episodeId" component={PodcastEpisode} />,
];

export default routes;
