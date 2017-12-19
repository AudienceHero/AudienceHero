import { createStore, combineReducers, applyMiddleware } from 'redux';
import compose from 'recompose/compose';
import React, { createElement, Component } from 'react';
import { reducer as formReducer } from 'redux-form';
import { createMuiTheme } from 'material-ui/styles';
import { all, fork } from 'redux-saga/effects';
import createSagaMiddleware from 'redux-saga';
import { Provider } from 'react-redux';
import {
    Layout,
    TranslationProvider,
    localeReducer,
} from '@audiencehero-frontoffice/core';

import PropTypes from 'prop-types';

import createHistory from 'history/createBrowserHistory';
import { Route } from 'react-router';

import {
    ConnectedRouter,
    routerReducer,
    routerMiddleware,
    push,
} from 'react-router-redux';
import merge from 'lodash.merge';
import { sagas, reducers, routes, bundleMessages } from './configuration';

// TODO: Change entrypoint based on environment
const entrypoint = '/api';

const messages = merge({ en: {} }, ...bundleMessages);
const theme = createMuiTheme();

class App extends Component {
    render() {
        const { routes, sagas, reducers, theme, locale, messages } = this.props;

        const appReducer = combineReducers({
            locale: localeReducer(locale),
            form: formReducer,
            routing: routerReducer,
            // ah_core: reducers.ah_core,
            ...reducers,
        });

        const appSaga = function* rootSaga() {
            yield all(sagas.map(fork));
        };

        const sagaMiddleware = createSagaMiddleware();

        const history = createHistory();
        const store = createStore(
            appReducer,
            {},
            compose(
                applyMiddleware(sagaMiddleware, routerMiddleware(history)),
                window.devToolsExtension ? window.devToolsExtension() : f => f
            )
        );

        sagaMiddleware.run(appSaga);

        return (
            <Provider store={store}>
                <TranslationProvider messages={messages}>
                    <ConnectedRouter history={history}>
                        <div>
                            <Route
                                path="/"
                                render={() =>
                                    createElement(Layout, { routes, theme })}
                            />
                        </div>
                    </ConnectedRouter>
                </TranslationProvider>
            </Provider>
        );
    }
}

App.propTypes = {
    routes: PropTypes.array,
    sagas: PropTypes.array,
    reducers: PropTypes.object,
    theme: PropTypes.object,
    locale: PropTypes.string,
    messages: PropTypes.object,
};

App.defaultProps = {
    reducers,
    routes,
    sagas,
    theme,
    messages,
    locale: 'en',
};

export default App;
