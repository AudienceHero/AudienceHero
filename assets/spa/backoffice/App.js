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
import Collapse from 'material-ui/transitions/Collapse';
import List, {ListItem, ListItemIcon, ListItemText} from 'material-ui/List';
import ExpandLess from 'material-ui-icons/ExpandLess';
import ExpandMore from 'material-ui-icons/ExpandMore';
import { getMuiTheme } from 'material-ui/styles';
import { Login, Layout, Dashboard } from '@audiencehero-backoffice/core';
import { hydraClient, fetchHydra, authProvider } from '@audiencehero/common';
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

// const theme = getMuiTheme();

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
        key={key}
        primaryText={translate(primaryText)}
        onClick={onMenuTap}
        to={to}
        leftIcon={React.createElement(leftIcon)}
    />
);

export class NestedMenu extends React.Component {
    state = { open: false };

    handleClick = () => {
        this.setState({ open: !this.state.open });
    };

    render() {
        const {menuItem, onMenuTap, translate} = this.props;
        const {menuItems} = menuItem;

        return (
            <div>
                <MenuItem button onClick={this.handleClick}>
                    <ListItemIcon>
                        {React.createElement(menuItem.leftIcon)}
                    </ListItemIcon>
                    <ListItemText inset primary={translate(menuItem.primaryText)} />
                    {this.state.open ? <ExpandLess /> : <ExpandMore />}
                </MenuItem>
                <Collapse in={this.state.open} timeout="auto" unmountOnExit>
                    <List component="div" disablePadding>
                    {menuItems.map(subMenu => {
                        return menuItemLinkRenderer({...subMenu, onMenuTap, key: subMenu.primaryText, translate});
                    })}
                    </List>
                </Collapse>
            </div>
        )
    }
}

export class MenuRenderer extends React.Component {
    render() {
        const { translate, onMenuTap, logout } = this.props;
        return (
            <div>
                <DashboardMenuItem onClick={onMenuTap} />
                <Divider />
                {menu.map(menuItem => {
                    if (menuItem.hasOwnProperty('menuItems')) {
                        return <NestedMenu key={menuItem.primaryText} menuItem={menuItem} onMenuTap={onMenuTap} translate={translate} />
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
                <NestedMenu
                    menuItem={{
                        primaryText: translate('ah.core.menu.settings'),
                        leftIcon: IconSettings,
                        menuItems: settingsMenu
                    }}
                    onMenuTap={onMenuTap}
                    translate={translate}
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
                // theme={theme}
                title="AudienceHero"
                dashboard={Dashboard}
                dataProvider={hydraClient(entrypoint, fetchWithAuth)}
                authProvider={authProvider}
            >
                {resources.map(item => {
                    return <Resource key={item.name} {...item} />;
                })}
            </Admin>
        );
    }
}

export default App;
