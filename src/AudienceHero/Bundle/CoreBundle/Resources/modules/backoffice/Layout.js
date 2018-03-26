import React, { Component, createElement } from 'react';
import PropTypes from 'prop-types';
import { connect } from 'react-redux';
import {LinearProgress} from 'material-ui/Progress';
import {withWidth, withStyles, createMuiTheme, getMuiTheme, MuiThemeProvider} from 'material-ui/styles';
import compose from 'recompose/compose';

import {
    AppBar,
    Sidebar,
    Notification,
    AdminRoutes,
    setSidebarVisibility as setSidebarVisibilityAction,
} from 'react-admin';

const styles = theme => {
    return {
        wrapper: {
            // Avoid IE bug with Flexbox, see #467
            display: 'flex',
            flexDirection: 'column',
        },
        main: {
            display: 'flex',
            flexDirection: 'column',
            minHeight: '100vh',
        },
        body: {
            backgroundColor: '#edecec',
            display: 'flex',
            flex: 1,
            overflowY: 'hidden',
            overflowX: 'scroll',
        },
        bodySmall: {
            backgroundColor: '#fff',
        },
        content: {
            flex: 1,
            padding: '2em',
        },
        contentSmall: {
            flex: 1,
            paddingTop: '3em',
        },
        linearLoader: {
            position: 'absolute',
            top: 0,
            left: 0,
            zIndex: 1200,
            height: '3px',
            maxHeight: '3px',
        },
        loader: {
            position: 'absolute',
            top: 0,
            right: 0,
            margin: 16,
            zIndex: 1200,
        },
    }
};

const prefixedStyles = {};

const defaultTheme = createMuiTheme({});

class Layout extends Component {
    componentWillMount() {
        if (this.props.width !== 1) {
            this.props.setSidebarVisibility(true);
        }
    }

    render() {
        const {
            classes,
            children,
            customRoutes,
            dashboard,
            isLoading,
            menu,
            catchAll,
            logout,
            theme,
            title,
            width,
        } = this.props;

        return (
            <MuiThemeProvider theme={theme}>
                <div className={classes.wrapper}>
                    {isLoading && (
                        <LinearProgress
                            color={loaderColor}
                            mode="indeterminate"
                            className={classes.linearLoader}
                        />
                    )}
                    <div className={classes.main}>
                        {width !== 1 && <AppBar title={title} />}
                        <div className={classes.body}>
                            <div>
                                <AdminRoutes
                                    customRoutes={customRoutes}
                                    dashboard={dashboard}
                                    catchAll={catchAll}
                                >
                                    {children}
                                </AdminRoutes>
                            </div>
                            <Sidebar>
                                {createElement(menu, {
                                    logout,
                                    hasDashboard: !!dashboard,
                                })}
                            </Sidebar>
                        </div>
                        <Notification />
                    </div>
                </div>
            </MuiThemeProvider>
        );
    }
}

const componentPropType = PropTypes.oneOfType([
    PropTypes.func,
    PropTypes.string,
]);

Layout.propTypes = {
    authClient: PropTypes.func,
    children: PropTypes.oneOfType([PropTypes.func, PropTypes.node]),
    catchAll: componentPropType,
    customRoutes: PropTypes.array,
    dashboard: componentPropType,
    isLoading: PropTypes.bool.isRequired,
    menu: PropTypes.oneOfType([PropTypes.func, PropTypes.string]),
    setSidebarVisibility: PropTypes.func.isRequired,
    title: PropTypes.node.isRequired,
    theme: PropTypes.object.isRequired,
    width: PropTypes.number,
    logout: PropTypes.oneOfType([
        PropTypes.node,
        PropTypes.func,
        PropTypes.string,
    ]),
};

Layout.defaultProps = {
    theme: defaultTheme,
};

function mapStateToProps(state) {
    return {
        isLoading: state.admin.loading > 0,
    };
}

const enhance = compose(
    connect(mapStateToProps, {
        setSidebarVisibility: setSidebarVisibilityAction,
    }),
    withStyles(styles),
);

export default enhance(Layout);
