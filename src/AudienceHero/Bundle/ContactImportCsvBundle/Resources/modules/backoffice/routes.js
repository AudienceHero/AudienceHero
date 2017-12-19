import React from 'react';
import { Route } from 'react-router-dom';
import CsvContactImport from './CsvContactImport';

export default [
    <Route
        exact
        path="/contacts/import/csv/:id"
        component={CsvContactImport}
    />,
];
