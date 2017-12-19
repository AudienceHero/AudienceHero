import React from 'react';
import { Route } from 'react-router-dom';
import Register from './Auth/Register';
import ForgotPassword from './Auth/ForgotPassword';
import ResetPassword from './Auth/ResetPassword';
import PersonEmailVerify from './Resources/PersonEmailVerify';
import ImportList from './Import/ImportList';

export default [
    <Route exact path="/register" component={Register} noLayout />,
    <Route exact path="/forgot-password" component={ForgotPassword} noLayout />,
    <Route
        exact
        path="/reset_password/:confirmationToken"
        component={ResetPassword}
        noLayout
    />,
    <Route
        exact
        path="/person_emails/:id/verify/:confirmationToken"
        component={PersonEmailVerify}
    />,
    <Route exact path="/import" component={ImportList} />,
];
