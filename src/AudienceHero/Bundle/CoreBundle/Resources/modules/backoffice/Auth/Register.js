import React, { Component } from 'react';
import PropTypes from 'prop-types';
import { propTypes, reduxForm, Field } from 'redux-form';
import { connect } from 'react-redux';
import compose from 'recompose/compose';

import MuiThemeProvider from 'material-ui/styles/MuiThemeProvider';
import { Card, CardActions } from 'material-ui/Card';
import Avatar from 'material-ui/Avatar';
import {CircularProgress} from 'material-ui/Progress';
import LockIcon from 'material-ui-icons/LockOutline';
import cyan from 'material-ui/colors/cyan';
import pink from 'material-ui/colors/pink';
const cyan500 = cyan['500'];
const pinkA200 = pink.A200;
import Divider from 'material-ui/Divider';

import { defaultTheme, translate, Notification } from 'react-admin';
import { userRegister as userRegisterAction } from '../actions';
import { styles, getColorsFromTheme, renderInput } from '../utils';
import { push } from 'react-router-redux';

class Register extends Component {
    register = creds => this.props.userRegister(creds);

    render() {
        const {
            handleSubmit,
            submitting,
            title,
            theme,
            translate,
        } = this.props;
        const { primary1Color, accent1Color } = getColorsFromTheme();
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
                        <form onSubmit={handleSubmit(this.register)}>
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
                                        name="email"
                                        component={renderInput}
                                        floatingLabelText={translate(
                                            'ah.auth.email'
                                        )}
                                        type="email"
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
                                    label={translate('ah.auth.register')}
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
                                    label={translate('ah.auth.sign_in')}
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

Register.propTypes = {
    ...propTypes,
    previousRoute: PropTypes.string,
    theme: PropTypes.object,
    translate: PropTypes.func.isRequired,
    userRegister: PropTypes.func.isRequired,
};

Register.defaultProps = {};

const enhance = compose(
    translate,
    reduxForm({
        form: 'register',
        validate: (values, props) => {
            const errors = {};
            const { translate } = props;
            if (!values.username) {
                errors.username = translate('aor.validation.required');
            }
            if (!values.email) {
                errors.username = translate('aor.validation.required');
            }
            if (!values.password) {
                errors.password = translate('aor.validation.required');
            }

            return errors;
        },
    }),
    connect(null, { userRegister: userRegisterAction })
);

export default enhance(Register);
