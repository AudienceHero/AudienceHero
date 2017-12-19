import React, { Component } from 'react';
import PropTypes from 'prop-types';
import { propTypes, reduxForm, Field } from 'redux-form';
import { connect } from 'react-redux';
import compose from 'recompose/compose';

import MuiThemeProvider from 'material-ui/styles/MuiThemeProvider';
import getMuiTheme from 'material-ui/styles/getMuiTheme';
import { Card, CardActions } from 'material-ui/Card';
import Avatar from 'material-ui/Avatar';
import {CircularProgress} from 'material-ui/Progress';
import LockIcon from 'material-ui-icons/LockOutline';
import cyan from 'material-ui/colors/cyan';
import pink from 'material-ui/colors/pink';
const cyan500 = cyan['500'];
const pinkA200 = pink.A200;

import { defaultTheme, translate, Notification } from 'react-admin';
import { resetUserPassword as resetUserPasswordAction } from '../actions';
import { styles, getColorsFromTheme, renderInput } from '../utils';
import { push } from 'react-router-redux';
import { withRouter } from 'react-router-dom';

class ResetPassword extends Component {
    resetUserPassword = creds =>
        this.props.resetUserPassword({
            confirmationToken: this.state.confirmationToken,
            ...creds,
        });

    state = {
        confirmationToken: null,
    };

    componentDidMount() {
        const { confirmationToken } = this.props.match.params;
        this.setState({ confirmationToken });
    }

    render() {
        const {
            handleSubmit,
            submitting,
            title,
            theme,
            translate,
        } = this.props;
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
                        <form onSubmit={handleSubmit(this.resetUserPassword)}>
                            <div style={styles.form}>
                                <div style={styles.input}>
                                    <Field
                                        name="plainPassword"
                                        component={renderInput}
                                        floatingLabelText={translate(
                                            'aor.auth.password'
                                        )}
                                        type="password"
                                        disabled={submitting}
                                    />
                                </div>
                                <div style={styles.input}>
                                    <Field
                                        name="plainPasswordRepeat"
                                        component={renderInput}
                                        floatingLabelText={translate(
                                            'aor.auth.repeat_password'
                                        )}
                                        type="password"
                                        disabled={submitting}
                                    />
                                </div>
                            </div>
                            <CardActions>
                                <Button raised
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
                                    label={translate('ah.auth.reset_password')}
                                    fullWidth
                                />
                                <p
                                    style={{
                                        color: 'rgba(0, 0, 0, 0.54)',
                                        marginTop: '1em',
                                    }}
                                >
                                    {translate('ah.auth.already_account?')}
                                </p>
                                <Button raised
                                    disabled={submitting}
                                    icon={
                                        submitting && (
                                            <CircularProgress
                                                size={25}
                                                thickness={2}
                                            />
                                        )
                                    }
                                    onTouchTap={() => {
                                        this.props.dispatch(push('/login'));
                                    }}
                                    label={translate('ah.auth.sign-in')}
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

ResetPassword.propTypes = {
    ...propTypes,
    previousRoute: PropTypes.string,
    theme: PropTypes.object,
    translate: PropTypes.func.isRequired,
    resetUserPassword: PropTypes.func.isRequired,
};

ResetPassword.defaultProps = {};

const enhance = compose(
    translate,
    reduxForm({
        form: 'register',
        validate: (values, props) => {
            const errors = {};
            const { translate } = props;
            if (!values.plainPassword) {
                errors.plainPassword = translate('aor.validation.required');
            }
            if (!values.plainPasswordRepeat) {
                errors.plainPasswordRepeat = translate(
                    'aor.validation.required'
                );
            }
            if (values.plainPassword != values.plainPasswordRepeat) {
                errors.plainPasswordRepeat = translate(
                    'ah.validation.passwords_must_match'
                );
            }

            return errors;
        },
    }),
    withRouter,
    connect(null, { resetUserPassword: resetUserPasswordAction })
);

export default enhance(ResetPassword);
