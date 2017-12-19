import React from 'react';
import PropTypes from 'prop-types';
import { Link } from 'react-router-dom';
import shouldUpdate from 'recompose/shouldUpdate';
import compose from 'recompose/compose';
import Button from "material-ui/Button"
import Dialog from 'material-ui/Dialog';
import SendIcon from 'material-ui-icons/Loupe';
import CancelIcon from 'material-ui-icons/Cancel';
import { translate, linkToRecord } from 'react-admin';
import { reduxForm, propTypes, Field } from 'redux-form';
import { connect } from 'react-redux';
import { sendPromoPreview as sendPromoPreviewAction } from './actions';
import { renderInput } from '@audiencehero-backoffice/core';
import get from 'lodash.get';

export class PreviewButton extends React.Component {
    state = {
        open: false,
    };

    handleOpen = () => {
        this.setState({ open: true });
    };

    handleClose = () => {
        this.setState({ open: false });
    };

    handleSendPreview = data => {
        const { record } = this.props;
        this.props.sendPromoPreview({ ...record, ...data });
        this.handleClose();
    };

    render() {
        const {
            record,
            handleSubmit,
            submitting,
            label,
            translate,
        } = this.props;
        const handleSubmitForm = handleSubmit(this.handleSendPreview);
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
                onTouchTap={handleSubmitForm}
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
                    title={translate('ah.promo.dialog.send_preview.title')}
                    modal={false}
                    onRequestClose={this.handleClose}
                    autoScrollBodyContent={true}
                    actions={actions}
                >
                    <p>{translate('ah.promo.dialog.send_preview.explain')}</p>
                    <form onSubmit={handleSubmitForm}>
                        <Field
                            name="testRecipient"
                            type="email"
                            component={renderInput}
                            disabled={submitting}
                            floatingLabelText={translate(
                                'ah.promo.input.test_recipient'
                            )}
                        />
                    </form>
                </Dialog>
            </div>
        );
    }
}

PreviewButton.defaultProps = {
    basePath: '',
    label: 'ah.promo.action.send_preview',
    record: {},
};

PreviewButton.propTypes = {
    ...propTypes,
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
    reduxForm({
        form: 'promo-preview',
        validate: (values, props) => {
            const errors = {};
            if (!values.testRecipient) {
                errors.testRecipient = translate('aor.validation.required');
            }
            return errors;
        },
    }),
    connect(null, { sendPromoPreview: sendPromoPreviewAction })
);

export default enhance(PreviewButton);
