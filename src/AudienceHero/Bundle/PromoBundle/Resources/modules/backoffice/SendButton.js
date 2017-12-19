import React from 'react';
import PropTypes from 'prop-types';
import { Link } from 'react-router-dom';
import shouldUpdate from 'recompose/shouldUpdate';
import compose from 'recompose/compose';
import Button from "material-ui/Button"
import Dialog from 'material-ui/Dialog';
import SendIcon from 'material-ui-icons/Send';
import CancelIcon from 'material-ui-icons/Cancel';
import { translate, linkToRecord } from 'react-admin';
import { connect } from 'react-redux';
import { sendPromo as sendPromoAction } from './actions';
import get from 'lodash.get';

export class SendButton extends React.Component {
    state = {
        open: false,
    };

    handleOpen = () => {
        this.setState({ open: true });
    };

    handleClose = () => {
        this.setState({ open: false });
    };

    handleSend = () => {
        const { sendPromo, record } = this.props;
        sendPromo(record.id);
        this.handleClose();
    };

    render() {
        const { record, label, translate } = this.props;
        const actions = [
            <Button
                icon={<CancelIcon />}
                label={translate('ah.core.button.cancel')}
                onTouchTap={this.handleClose}
            />,
            <Button
                icon={<SendIcon />}
                label={translate('ah.promo.button.send')}
                primary
                onTouchTap={this.handleSend}
            />,
        ];

        if ('draft' !== get(record, 'mailing.status')) {
            return false;
        }

        return (
            <div>
                <Button
                    primary
                    label={label && translate(label)}
                    icon={<SendIcon />}
                    onTouchTap={this.handleOpen}
                    style={{ overflow: 'inherit' }}
                />
                <Dialog
                    open={this.state.open}
                    title={translate('ah.promo.dialog.send_confirm.title')}
                    modal={false}
                    autoScrollBodyContent={true}
                    onRequestClose={this.handleClose}
                    actions={actions}
                >
                    <p>{translate('ah.promo.dialog.send_confirm.explain')}</p>
                </Dialog>
            </div>
        );
    }
}

SendButton.defaultProps = {
    basePath: '',
    label: 'ah.promo.action.send',
    record: {},
};

SendButton.propTypes = {
    label: PropTypes.string,
    record: PropTypes.object,
    translate: PropTypes.func.isRequired,
};

const enhance = compose(
    shouldUpdate(
        (props, nextProps) =>
            (props.record && props.record.id !== nextProps.record.id) ||
            (props.record == null && nextProps.record != null)
    ),
    translate,
    connect(null, { sendPromo: sendPromoAction })
);

export default enhance(SendButton);
