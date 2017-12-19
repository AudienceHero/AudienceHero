import React from 'react';
import { Route } from 'react-router';
import {
    OptinRequest,
    OptinRequestConfirm,
    OptinRequestConfirmed,
} from './Optin';

const routes = [
    <Route exact path="/forms/:id" component={OptinRequest} />,
    <Route
        exact
        path="/forms/:id/request-confirm"
        component={OptinRequestConfirm}
    />,
    <Route
        exact
        path="/forms/:id/request-confirmed"
        component={OptinRequestConfirmed}
    />,
];

export default routes;
