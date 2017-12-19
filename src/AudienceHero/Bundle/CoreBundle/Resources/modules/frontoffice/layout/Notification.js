// Taken from Admin-On-Rest.
// Licensed under the MIT license.
//
// Copyright (c) 2016-present, Francois Zaninotto, Marmelab

import React from 'react';
import PropTypes from 'prop-types';
import { connect } from 'react-redux';
import Snackbar from 'material-ui/Snackbar';
import { hideNotification as hideNotificationAction } from '../actions/notificationActions';
import translate from '../i18n/translate';

class Notification extends React.Component {
    handleRequestClose = () => {
        this.props.hideNotification();
    };

    render() {
        const style = {};
        const { type, translate, message } = this.props;
        if (type === 'warning') {
        }
        if (type === 'confirm') {
        }
        return (
            <Snackbar
                open={!!message}
                message={!!message && translate(message)}
                autoHideDuration={4000}
                onRequestClose={this.handleRequestClose}
                bodyStyle={style}
            />
        );
    }
}

Notification.propTypes = {
    message: PropTypes.string,
    type: PropTypes.string.isRequired,
    hideNotification: PropTypes.func.isRequired,
    translate: PropTypes.func.isRequired,
};

Notification.defaultProps = {
    type: 'info',
};

const mapStateToProps = state => ({
    message: state.ah_core.notification.text,
    type: state.ah_core.notification.type,
});

export default translate(
    connect(mapStateToProps, { hideNotification: hideNotificationAction })(
        Notification
    )
);
