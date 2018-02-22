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

import { defaultTheme, translate, Notification } from 'react-admin';
import { styles, getColorsFromTheme, renderInput } from '../utils';
import { push } from 'react-router-redux';
import { resetUserPasswordRequest as resetUserPasswordRequestAction } from '../actions';

class ForgotPassword extends Component {
    resetPassword = data => this.props.resetUserPasswordRequest(data);

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
                        <form onSubmit={handleSubmit(this.resetPassword)}>
                            <div style={styles.form}>
                                <div style={styles.input}>
                                    <Field
                                        name="email"
                                        component={renderInput}
                                        floatingLabelText={translate(
                                            'ah.auth.username_or_email'
                                        )}
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
                                    {translate('ah.auth.no_account?')}
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
                                    label={translate('ah.auth.sign_in')}
                                    onTouchTap={() => {
                                        this.props.dispatch(push('/login'));
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

ForgotPassword.propTypes = {
    ...propTypes,
    previousRoute: PropTypes.string,
    theme: PropTypes.object,
    translate: PropTypes.func.isRequired,
    resetUserPasswordRequest: PropTypes.func.isRequired,
};

ForgotPassword.defaultProps = {};

const enhance = compose(
    translate,
    reduxForm({
        form: 'forgotPassword',
        validate: (values, props) => {
            const errors = {};
            const { translate } = props;
            if (!values.username) {
                errors.username = translate('aor.validation.required');
            }

            return errors;
        },
    }),
    connect(null, { resetUserPasswordRequest: resetUserPasswordRequestAction })
);

export default enhance(ForgotPassword);
