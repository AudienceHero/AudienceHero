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
import { sendMailingPreview as sendMailingPreviewAction } from './actions';
import { renderInput } from '@audiencehero-backoffice/core';

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
        this.props.sendMailingPreview({ ...record, ...data });
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
                label={translate(
                    'ah.mailing.dialog.send_preview.button.send_preview'
                )}
                primary
                onTouchTap={handleSubmitForm}
            />,
        ];

        if ('draft' !== record.status) {
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
                    title={translate('ah.mailing.dialog.send_preview.title')}
                    modal={false}
                    onRequestClose={this.handleClose}
                    autoScrollBodyContent={true}
                    actions={actions}
                >
                    <p>{translate('ah.mailing.dialog.send_preview.explain')}</p>
                    <form onSubmit={handleSubmitForm}>
                        <Field
                            name="testRecipient"
                            type="email"
                            component={renderInput}
                            disabled={submitting}
                            floatingLabelText={translate(
                                'ah.mailing.dialog.send_preview.input.test_recipient'
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
    label: 'ah.mailing.action.send_preview',
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
        form: 'mailing-preview',
        validate: (values, props) => {
            const errors = {};
            if (!values.testRecipient) {
                errors.testRecipient = translate('aor.validation.required');
            }
            return errors;
        },
    }),
    connect(null, { sendMailingPreview: sendMailingPreviewAction })
);

export default enhance(PreviewButton);
