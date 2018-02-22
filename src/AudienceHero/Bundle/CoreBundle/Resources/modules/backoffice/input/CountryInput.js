import React, { Component } from 'react';
import PropTypes from 'prop-types';
import get from 'lodash.get';
import {Select} from 'material-ui';
import compose from 'recompose/compose';
import { fetchCountryList as fetchCountryListAction } from '../actions';
import { connect } from 'react-redux';
import { addField, Field } from 'redux-form';
import { translate, FieldTitle } from 'react-admin';

export class CountryInput extends Component {
    /*
     * Using state to bypass a redux-form comparison but which prevents re-rendering
     * @see https://github.com/erikras/redux-form/issues/2456
     */
    state = {
        value: this.props.input.value,
    };

    componentWillMount() {
        this.props.fetchCountryList(this.props.locale);
    }

    handleChange = (event, index, value) => {
        this.props.input.onChange(value);
        this.setState({ value });
    };

    render() {
        const {
            allowEmpty,
            elStyle,
            locale,
            countries,
            isRequired,
            label,
            meta,
            options,
            resource,
            source,
        } = this.props;
        if (typeof meta === 'undefined') {
            throw new Error(
                "The CountryInput component wasn't called within a redux-form <Field>. Did you decorate it and forget to add the addField prop to your component? See https://marmelab.com/react-admin/Inputs.html#writing-your-own-input-component for details."
            );
        }
        const { touched, error } = meta;
        let choices = [];
        if (countries[locale]) {
            choices = countries[locale];
        }

        return (
            <Select
                value={this.state.value}
                floatingLabelText={
                    <FieldTitle
                        label={label}
                        source={source}
                        resource={resource}
                        isRequired={isRequired}
                    />
                }
                onChange={this.handleChange}
                autoWidth
                style={elStyle}
                errorText={touched && error}
                {...options}
            >
                {allowEmpty && <MenuItem value={null} primaryText="" />}
                {Object.keys(choices).map(countryCode => (
                    <MenuItem
                        key={countryCode}
                        value={countryCode}
                        primaryText={choices[countryCode]}
                    />
                ))}
            </Select>
        );
    }
}

CountryInput.propTypes = {
    addField: PropTypes.bool.isRequired,
    allowEmpty: PropTypes.bool.isRequired,
    choices: PropTypes.arrayOf(PropTypes.object),
    elStyle: PropTypes.object,
    input: PropTypes.object,
    isRequired: PropTypes.bool,
    label: PropTypes.string,
    meta: PropTypes.object,
    options: PropTypes.object,
    resource: PropTypes.string,
    source: PropTypes.string,
    locale: PropTypes.string.isRequired,
    countries: PropTypes.arrayOf(PropTypes.object),
};

const enhance = compose(
    translate,
    addField,
    connect(
        ({ locale, ah_core }) => ({
            locale,
            countries: ah_core.i18n['countries'],
        }),
        {
            fetchCountryList: fetchCountryListAction,
        }
    )
);

const EnhancedCountryInput = enhance(CountryInput);

EnhancedCountryInput.defaultProps = {
    allowEmpty: false,
    options: {},
    locale: 'en',
};

export default EnhancedCountryInput;
