import React from 'react';
import compose from 'recompose/compose';
import PropTypes from 'prop-types';
import { Field, reduxForm } from 'redux-form';
import { withStyles } from 'material-ui/styles';
import { TextInput, translate } from '@audiencehero-frontoffice/core';
import Button from 'material-ui/Button';
import {
    DialogActions,
    DialogContent,
    DialogContentText,
    DialogTitle,
} from 'material-ui/Dialog';
import { CountryInput } from '@audiencehero-frontoffice/contact';

const styles = {
    form: {
        overflowY: 'scroll',
    },
};

export class UnlockForm extends React.Component {
    render() {
        const {
            handleSubmit,
            isSubmitting,
            classes,
            contactsGroupForm,
            translate,
        } = this.props;
        const {
            askCity,
            askCountry,
            askEmail,
            askName,
            askPhone,
        } = contactsGroupForm;
        return (
            <form className={classes.form} onSubmit={handleSubmit}>
                <DialogTitle>
                    {translate('ah.afd.dialog.download.title')}
                </DialogTitle>
                <DialogContent>
                    {askName && (
                        <Field
                            name="name"
                            component={TextInput}
                            props={{
                                translate,
                                label: 'ah.contact.input.name',
                                helperText: 'ah.contact.helper.name',
                            }}
                        />
                    )}
                    {askEmail && (
                        <Field
                            name="email"
                            component={TextInput}
                            props={{
                                translate,
                                label: 'ah.contact.input.email',
                                helperText: 'ah.contact.helper.email',
                            }}
                        />
                    )}
                    {askPhone && (
                        <Field
                            name="phone"
                            component={TextInput}
                            props={{
                                translate,
                                label: 'ah.contact.input.phone',
                                helperText: 'ah.contact.helper.phone',
                            }}
                        />
                    )}
                    {askCity && (
                        <Field
                            name="city"
                            component={TextInput}
                            props={{
                                translate,
                                label: 'ah.contact.input.city',
                                helperText: 'ah.contact.helper.city',
                            }}
                        />
                    )}
                    {askCountry && (
                        <Field
                            name="country"
                            component={CountryInput}
                            props={{
                                translate,
                                label: 'ah.contact.input.country',
                                helperText: 'ah.contact.helper.country',
                            }}
                        />
                    )}
                </DialogContent>
                <DialogActions>
                    <Button onClick={this.handleCloseDialog} color="primary">
                        {translate('ah.core.dialog.button.close')}
                    </Button>
                    <Button raised onClick={handleSubmit} color="primary">
                        {translate('ah.afd.dialog.download.action.download')}
                    </Button>
                </DialogActions>
            </form>
        );
    }
}

UnlockForm.propTypes = {
    translate: PropTypes.func.isRequired,
    contactsGroupForm: PropTypes.object.isRequired,
    onSubmit: PropTypes.func.isRequired,
    classes: PropTypes.object.isRequired,
    handleSubmit: PropTypes.func.isRequired, // passed by redux-form
    isSubmitting: PropTypes.bool.isRequired,
};

const enhance = compose(
    translate,
    reduxForm({ form: 'afd-unlock' }),
    withStyles(styles)
);

export default enhance(UnlockForm);
