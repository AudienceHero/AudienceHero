import React from 'react';
import PropTypes from 'prop-types';
import compose from 'recompose/compose';
import { connect } from 'react-redux';
import {
    fetchCountries as fetchCountriesAction,
    translate,
} from '@audiencehero-frontoffice/core';
import { FormControl, InputLabel, Select, Input } from 'material-ui';
import { withStyles } from 'material-ui/styles';

const styles = {
    wrapper: {
        width: '100%',
        marginTop: '1em',
        marginBottom: '1em',
    },
};

export class CountryInput extends React.Component {
    componentWillMount() {
        this.props.fetchCountries(this.props.locale);
    }

    render() {
        const {
            label,
            translate,
            classes,
            choices,
            input: { value, onChange },
        } = this.props;

        const countries = Object.keys(choices).map(key => (
            <option key={key} value={key}>
                {choices[key]}
            </option>
        ));

        return (
            <FormControl className={classes.wrapper}>
                <InputLabel htmlFor="country">{translate(label)}</InputLabel>
                <Select
                    native
                    input={<Input id="country" />}
                    value={value}
                    onChange={onChange}
                >
                    <option key="empty" value={null} />
                    {countries}
                </Select>
            </FormControl>
        );
    }
}

CountryInput.propTypes = {
    label: PropTypes.string.isRequired,
    locale: PropTypes.string.isRequired,
    fetchCountries: PropTypes.func.isRequired,
    choices: PropTypes.object.isRequired,
    input: PropTypes.object.isRequired, // passed by redux-form
    meta: PropTypes.object.isRequired, // passed by redux-form
};

const mapStateToProps = (state, props) => {
    const locale = state.locale;
    const choices = state.ah_core.i18n.countries;

    return { locale, choices };
};

const enhance = compose(
    connect(mapStateToProps, {
        fetchCountries: fetchCountriesAction,
    }),
    translate,
    withStyles(styles)
);

export default enhance(CountryInput);
