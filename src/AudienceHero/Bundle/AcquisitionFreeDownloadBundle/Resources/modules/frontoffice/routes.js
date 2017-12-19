import React from 'react';
import { Route } from 'react-router';
import AcquisitionFreeDownload from './AcquisitionFreeDownload';

const routes = [
    <Route
        exact
        path="/free-downloads/:id"
        component={AcquisitionFreeDownload}
    />,
    <Route
        exact
        path="/free-downloads/:id/:preview"
        component={AcquisitionFreeDownload}
    />,
];

export default routes;
