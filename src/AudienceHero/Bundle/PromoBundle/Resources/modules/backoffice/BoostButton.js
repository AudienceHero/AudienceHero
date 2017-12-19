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
import { boostPromo as boostPromoAction } from './actions';
import get from 'lodash.get';

export class BoostButton extends React.Component {
    state = {
        open: false,
    };

    handleOpen = () => {
        this.setState({ open: true });
    };

    handleClose = () => {
        this.setState({ open: false });
    };

    handleBoost = () => {
        const { boostPromo, record } = this.props;
        boostPromo(record.id);
        this.handleClose();
    };

    render() {
        const { record, label, translate } = this.props;
        const actions = [
            <Button
                icon={<CancelIcon />}
                label={translate('ah.button.cancel')}
                onTouchTap={this.handleClose}
            />,
            <Button
                icon={<SendIcon />}
                label={translate('ah.promo.button.boost')}
                primary
                onTouchTap={this.handleBoost}
            />,
        ];

        if (
            'draft' === get(record, 'mailing.status') ||
            'pending' === get(record, 'mailing.status') ||
            null !== get(record, 'mailing.boostMailing')
        ) {
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
                    title={translate('ah.promo.dialog.boost.title')}
                    modal={false}
                    autoScrollBodyContent={true}
                    onRequestClose={this.handleClose}
                    actions={actions}
                >
                    <p>{translate('ah.promo.dialog.boost.explain')}</p>
                </Dialog>
            </div>
        );
    }
}

BoostButton.defaultProps = {
    basePath: '',
    label: 'ah.promo.action.boost',
    record: {},
};

BoostButton.propTypes = {
    label: PropTypes.string,
    record: PropTypes.object,
    translate: PropTypes.func.isRequired,
};

const enhance = compose(
    translate,
    shouldUpdate(
        (props, nextProps) =>
            (props.record && props.record.id !== nextProps.record.id) ||
            (props.record == null && nextProps.record != null)
    ),
    connect(null, { boostPromo: boostPromoAction })
);

export default enhance(BoostButton);
