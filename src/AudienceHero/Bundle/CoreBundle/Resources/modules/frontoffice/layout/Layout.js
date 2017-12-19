import compose from 'recompose/compose';
import { connect } from 'react-redux';
import React, { createElement } from 'react';
import PropTypes from 'prop-types';
import { withStyles } from 'material-ui/styles';
import { MuiThemeProvider } from 'material-ui/styles';
import AppBar from 'material-ui/AppBar';
import { LinearProgress } from 'material-ui/Progress';
import Typography from 'material-ui/Typography';
import Toolbar from 'material-ui/Toolbar';
import { Switch, Route } from 'react-router';
import Notification from './Notification';

const styles = theme => ({
    content: theme.mixins.gutters({
        paddingTop: '2em',
        paddingBottom: '2em',
        flex: '1 1 100%',
        maxWidth: '100%',
        margin: '0 auto',
    }),
    [theme.breakpoints.down('md')]: {
        content: {
            paddingTop: '1em',
        },
    },
    [theme.breakpoints.up('md')]: {
        content: {
            maxWidth: 900,
        },
    },
});

class Layout extends React.Component {
    render() {
        const { title, theme, routes, isLoading, classes } = this.props;

        return (
            <MuiThemeProvider theme={theme}>
                <div>
                    <AppBar position="static">
                        <Toolbar>
                            <Typography type="title" color="inherit">
                                {title}
                            </Typography>
                        </Toolbar>
                        {isLoading && (
                            <LinearProgress color="accent" mode="query" />
                        )}
                    </AppBar>
                    <div className={classes.content}>
                        <Switch>
                            {routes.map(
                                (route, index) => (
                                    <Route
                                        key={index}
                                        exact={route.props.exact}
                                        path={route.props.path}
                                        component={route.props.component}
                                        render={route.props.render}
                                        children={route.props.children}
                                    />
                                ) // eslint-disable-line react/no-children-prop
                            )}
                        </Switch>
                    </div>
                    <Notification theme={theme} />
                </div>
            </MuiThemeProvider>
        );
    }
}

Layout.propTypes = {
    classes: PropTypes.object.isRequired,
    routes: PropTypes.array.isRequired,
    theme: PropTypes.object.isRequired,
    title: PropTypes.string,
    isLoading: PropTypes.bool,
};

const mapStateToProps = (state, props) => ({
    title: state.ah_core.title,
    isLoading: state.ah_core.loading > 0,
});

const enhance = compose(withStyles(styles), connect(mapStateToProps));

const EnhancedLayout = enhance(Layout);

export default EnhancedLayout;
