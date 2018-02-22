import { DashboardMenuItem, MenuItemLink } from 'react-admin';
import Divider from 'material-ui/Divider';
import {MenuItem} from 'material-ui/Menu';
import IconSettings from 'material-ui-icons/Settings';
import IconImport from 'material-ui-icons/ImportExport';
import IconArrowDropRight from 'material-ui-icons/ArrowDropDown';
import merge from 'lodash.merge';

// Icons
import React, { Component } from 'react';
import { Route } from 'react-router-dom';
import { createBrowserHistory } from 'history';
import { Admin, Resource, translate } from 'react-admin';
import { englishMessages } from 'react-admin';
import { getMuiTheme } from 'material-ui/styles';
import { Layout, Login, Dashboard } from '@audiencehero-backoffice/core';
import { hydraClient, fetchHydra, authClient } from '@audiencehero/common';
import {
    menu,
    resources,
    importMenu,
    bundleMessages,
    customReducers,
    customRoutes,
    customSagas,
    settingsMenu,
} from './configuration';
import injectTapEventPlugin from 'react-tap-event-plugin';

const entrypoint = '/api';

injectTapEventPlugin();

const fetchWithAuth = (url, options = {}) => {
    if (!options.headers) {
        options.headers = new Headers({ Accept: 'application/ld+json' });
    }

    options.headers.set(
        'Authorization',
        `Bearer ${localStorage.getItem('token')}`
    );

    return fetchHydra(url, options);
};

const history = createBrowserHistory({
    basename: '/admin',
});

/*
.dark-primary-color    { background: #0288D1; }
.default-primary-color { background: #03A9F4; }
.light-primary-color   { background: #B3E5FC; }
.text-primary-color    { color: #FFFFFF; }
.accent-color          { background: #FF4081; }
.primary-text-color    { color: #212121; }
.secondary-text-color  { color: #757575; }
.divider-color         { border-color: #BDBDBD; }
*/

const theme = getMuiTheme();

export const messages = merge({ en: englishMessages }, ...bundleMessages);
export const i18nprovider = lang => messages[lang];

const menuItemLinkRenderer = ({
    leftIcon,
    translate,
    primaryText,
    to,
    onMenuTap,
    key,
}) => (
    <MenuItemLink
        leftIcon={React.createElement(leftIcon)}
        primaryText={translate(primaryText)}
        to={to}
        onClick={onMenuTap}
        key={key}
    />
);

export class MenuRenderer extends React.Component {
    render() {
        const { translate, onMenuTap, logout } = this.props;
        return (
            <div>
                <DashboardMenuItem onClick={onMenuTap} />
                <Divider />
                {menu.map(menuItem => {
                    if (menuItem.hasOwnProperty('menuItems')) {
                        return (
                            <MenuItem
                                key={menuItem.primaryText}
                                primaryText={translate(menuItem.primaryText)}
                                leftIcon={React.createElement(
                                    menuItem.leftIcon
                                )}
                                rightIcon={<IconArrowDropRight />}
                                menuItems={menuItem.menuItems.map(submenu =>
                                    menuItemLinkRenderer({
                                        ...submenu,
                                        onMenuTap,
                                        key: submenu.primaryText,
                                        translate,
                                    })
                                )}
                            />
                        );
                    }

                    return menuItemLinkRenderer({
                        ...menuItem,
                        translate,
                        onMenuTap,
                        key: menuItem.primaryText,
                    });
                })}

                <Divider />
                {menuItemLinkRenderer({
                    to: '/import',
                    translate,
                    onMenuTap,
                    key: 'import',
                    leftIcon: IconImport,
                    primaryText: 'ah.core.menu.import',
                })}
                <Divider />
                <MenuItem
                    primaryText={translate('ah.core.menu.settings')}
                    leftIcon={<IconSettings />}
                    rightIcon={<IconArrowDropRight />}
                    menuItems={settingsMenu.map(submenu =>
                        menuItemLinkRenderer({
                            ...submenu,
                            translate,
                            onMenuTap,
                            key: submenu.primaryText,
                        })
                    )}
                />
                <Divider />
                <Divider />
                {logout}
            </div>
        );
    }
}

const i18nMenuRenderer = translate(MenuRenderer);

class App extends Component {
    state = { api: null };

    render() {
        return (
            <Admin
                menu={i18nMenuRenderer}
                i18nProvider={i18nprovider}
                appLayout={Layout}
                history={history}
                loginPage={Login}
                customRoutes={customRoutes}
                customReducers={customReducers}
                customSagas={customSagas}
                theme={theme}
                title="AudienceHero"
                dashboard={Dashboard}
                dataProvider={hydraClient(entrypoint, fetchWithAuth)}
                authProvider={authClient}
            >
                {resources.map(item => {
                    return <Resource key={item.name} {...item} />;
                })}
            </Admin>
        );
    }
}

export default App;
