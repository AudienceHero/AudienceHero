import React from 'react';
import { connect } from 'react-redux';
import compose from 'recompose/compose';
import PropTypes from 'prop-types';
import { propTypes, reduxForm, Field } from 'redux-form';
import { Card } from 'material-ui/Card';
import MuiThemeProvider from 'material-ui/styles/MuiThemeProvider';
import getMuiTheme from 'material-ui/styles/getMuiTheme';
import cyan from 'material-ui/colors/cyan';
import pink from 'material-ui/colors/pink';
const cyan500 = cyan['500'];
const pinkA200 = pink.A200;
import {
    userLogin as userLoginAction,
    defaultTheme,
    translate,
    Notification,
} from 'react-admin';
import Avatar from 'material-ui/Avatar';
import { CardActions } from 'material-ui/Card';
import { push } from 'react-router-redux';
import {CircularProgress} from 'material-ui/Progress';
import LockIcon from 'material-ui-icons/LockOutline';
import { styles, getColorsFromTheme, renderInput } from '../utils';

class Login extends React.Component {
    login = auth =>
        this.props.userLogin(
            auth,
            this.props.location.state
                ? this.props.location.state.nextPathname
                : '/'
        );

    render() {
        const { handleSubmit, submitting, theme, translate } = this.props;
        const muiTheme = getMuiTheme(theme);
        const { primary1Color, accent1Color } = getColorsFromTheme(muiTheme);
        return (
            <MuiThemeProvider muiTheme={muiTheme}>
                <div style={{ ...styles.main, backgroundColor: primary1Color }}>
                    <Card style={styles.card}>
                        <div style={styles.avatar}>
                            <Avatar
                                backgroundColor={accent1Color}
                                icon={<LockIcon />}
                                size={60}
                            />
                        </div>
                        <form onSubmit={handleSubmit(this.login)}>
                            <div style={styles.form}>
                                <div style={styles.input}>
                                    <Field
                                        name="username"
                                        component={renderInput}
                                        floatingLabelText={translate(
                                            'aor.auth.username'
                                        )}
                                        disabled={submitting}
                                    />
                                </div>
                                <div style={styles.input}>
                                    <Field
                                        name="password"
                                        component={renderInput}
                                        floatingLabelText={translate(
                                            'aor.auth.password'
                                        )}
                                        type="password"
                                        disabled={submitting}
                                    />
                                </div>
                            </div>
                            <CardActions>
                                <Button raised
                                    className="ah-button-login"
                                    type="submit"
                                    primary
                                    disabled={submitting}
                                    icon={
                                        submitting && (
                                            <CircularProgress
                                                size={25}
                                                thickness={2}
                                            />
                                        )
                                    }
                                    label={translate('ra.auth.sign_in')}
                                    fullWidth
                                />
                                <Button raised
                                    style={{ marginTop: '1em' }}
                                    icon={
                                        submitting && (
                                            <CircularProgress
                                                size={25}
                                                thickness={2}
                                            />
                                        )
                                    }
                                    disabled={submitting}
                                    label={translate('ah.auth.problem?')}
                                    onTouchTap={() => {
                                        this.props.dispatch(
                                            push('/forgot-password')
                                        );
                                    }}
                                    fullWidth
                                />
                                <Button raised
                                    style={{ marginTop: '1em' }}
                                    icon={
                                        submitting && (
                                            <CircularProgress
                                                size={25}
                                                thickness={2}
                                            />
                                        )
                                    }
                                    disabled={submitting}
                                    label={translate('ah.auth.create_account')}
                                    onTouchTap={() => {
                                        this.props.dispatch(push('/register'));
                                    }}
                                    fullWidth
                                />
                            </CardActions>
                        </form>
                    </Card>
                    <Notification />
                </div>
            </MuiThemeProvider>
        );
    }
}

Login.propTypes = {
    ...propTypes,
    authClient: PropTypes.func,
    previousRoute: PropTypes.string,
    theme: PropTypes.object.isRequired,
    translate: PropTypes.func.isRequired,
    userLogin: PropTypes.func.isRequired,
};

Login.defaultProps = {
};

const enhance = compose(
    translate,
    reduxForm({
        form: 'signIn',
        validate: (values, props) => {
            const errors = {};
            const { translate } = props;
            if (!values.username)
                errors.username = translate('ra.validation.required');
            if (!values.password)
                errors.password = translate('ra.validation.required');
            return errors;
        },
    }),
    connect(null, { userLogin: userLoginAction })
);

export default enhance(Login);
