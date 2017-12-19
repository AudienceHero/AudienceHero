import React from 'react';
import Button from "material-ui/Button"
import PropTypes from 'prop-types';
import { connect } from 'react-redux';
import { translate } from 'react-admin';
import compose from 'recompose/compose';
import { sendPersonEmailVerification as sendPersonEmailVerificationAction } from '../actions';

export class PersonEmailSendVerificationMailButton extends React.Component {
    handleTouchTap = () => {
        this.props.sendPersonEmailVerification(this.props.record.id);
    };

    render() {
        if (this.props.record.isVerified) {
            return false;
        }

        return (
            <Button
                label={this.props.translate(
                    'ah.person_email.send_verification_email'
                )}
                onTouchTap={this.handleTouchTap}
            />
        );
    }
}

PersonEmailSendVerificationMailButton.propTypes = {
    sendPersonEmailVerification: PropTypes.func.isRequired,
    record: PropTypes.object.isRequired,
    translate: PropTypes.func.isRequired,
};

const enhance = compose(
    translate,
    connect(null, {
        sendPersonEmailVerification: sendPersonEmailVerificationAction,
    })
);

export default enhance(PersonEmailSendVerificationMailButton);
