import React from 'react';
import { Route } from 'react-router';
import Promo from './Promo';

const routes = [
    <Route exact path="/promos/:id/:recipientId" component={Promo} />,
];

export default routes;
