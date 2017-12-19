import React from 'react';
import { connect } from 'react-redux';
import PropTypes from 'prop-types';
import compose from 'recompose/compose';
import { reduxForm, Field, change as changeAction } from 'redux-form';
import Button from 'material-ui/Button';
import { translate, TextInput } from '@audiencehero-frontoffice/core';
import { CountryInput } from '@audiencehero-frontoffice/contact';
import get from 'lodash.get';

export class ContactsGroupForm extends React.Component {
    state = {
        guessedCity: null,
        guessedCountry: null,
    };

    componentWillReceiveProps(nextProps) {
        const askCity = get(nextProps, 'data.askCity');
        const askCountry = get(nextProps, 'data.askCountry');
        const guessedCity = get(nextProps, 'data.guessedCity');
        const guessedCountry = get(nextProps, 'data.guessedCountry');

        if (askCity && guessedCity && guessedCity != this.state.guessedCity) {
            this.props.change('optin', 'city', guessedCity);
        }

        if (
            askCountry &&
            guessedCountry &&
            guessedCountry != this.state.guessedCountry
        ) {
            this.props.change('optin', 'country', guessedCountry);
        }
    }

    render() {
        const { data, translate, handleSubmit } = this.props;
        const { askCity, askCountry, askEmail, askName, askPhone } = data;

        return (
            <form onSubmit={handleSubmit}>
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

                <Button raised onClick={handleSubmit} color="primary">
                    {translate('ah.contact.button.optin')}
                </Button>
            </form>
        );
    }
}

ContactsGroupForm.propTypes = {
    translate: PropTypes.func.isRequired,
    data: PropTypes.object.isRequired,
    onSubmit: PropTypes.func.isRequired,
    handleSubmit: PropTypes.func.isRequired,
};

const enhance = compose(
    translate,
    reduxForm({ form: 'optin' }),
    connect(null, {
        change: changeAction,
    })
);

export default enhance(ContactsGroupForm);
