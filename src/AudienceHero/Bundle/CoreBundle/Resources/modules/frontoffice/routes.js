import React from 'react';
import Unauthorized from './auth/Unauthorized';
import { Route } from 'react-router';

const routes = [<Route exact path="/403" component={Unauthorized} />];

export default routes;
