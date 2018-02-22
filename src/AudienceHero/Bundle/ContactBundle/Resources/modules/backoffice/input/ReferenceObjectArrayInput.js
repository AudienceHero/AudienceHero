import React from 'react';
import PropTypes from 'prop-types';
import {
    translate,
    crudGetMany as crudGetManyAction,
    crudGetMatching as crudGetMatchingAction,
    SelectArrayInput,
    FieldTitle,
    addField,
} from 'react-admin';
import { connect } from 'react-redux';
import compose from 'recompose/compose';
import { change as changeAction } from 'redux-form';
import get from 'lodash.get';

export class ReferenceObjectArrayInput extends React.Component {
    state = {
        values: [],
        normalized: [],
        initialized: false,
    };

    componentDidMount() {
        this.fetchReferenceAndOptions();
    }

    componentWillReceiveProps = nextProps => {
        // If the component has already been altered, do nothing.
        if (this.state.initialized) {
            return;
        }

        // Initialize the component value based on the record data.
        const existing = get(nextProps, `record.${nextProps.source}`);
        if (!existing) {
            return;
        }

        const values = this.state.values;
        for (const existingReference of existing) {
            const refId = get(
                existingReference,
                `${this.props.referenceKey}.${this.props.optionValue}`
            );
            if (-1 === values.findIndex(element => element === refId)) {
                values.push({
                    text: get(
                        existingReference,
                        `${this.props.referenceKey}.${this.props.optionText}`
                    ),
                    value: refId,
                });
            }
        }

        this.setState({
            initialized: true,
            values,
            normalized: this.normalize(values),
        });
    };

    normalize = extracted => {
        const normalized = [];
        const existing = get(this.props, `record.${this.props.source}`) || [];
        const available = this.props.referenceRecords;

        refLoop: for (const value of extracted) {
            // Check if the value can be extracted from 'existing' records
            // If not, extract value from the available records
            for (const existingReference of existing) {
                if (
                    value ===
                    get(
                        existingReference,
                        `${this.props.referenceKey}.${this.props.optionValue}`
                    )
                ) {
                    normalized.push(existingReference);
                    continue refLoop;
                }
            }

            const availableIRI = get(
                available,
                `${value}.${this.props.normalizedKey}`
            );
            if (availableIRI) {
                const ref = {};
                ref[this.props.referenceKey] = availableIRI;
                normalized.push(ref);
            }
        }

        return normalized;
    };

    handleChange = eventOrValue => {
        const extracted = this.extractIds(eventOrValue);
        const normalized = this.normalize(extracted);
        this.setState({ normalized });
        this.props.change(this.props.formName, this.props.source, normalized);
    };

    handleBlur = () => {
        const extracted = this.extractIds(this.state.values);
        this.props.input.onBlur(this.normalize(extracted));
    };

    handleFocus = () => {
        const extracted = this.extractIds(this.state.values);
        // this.props.onFocus(extracted);
        this.props.input.onFocus(this.normalize(extracted));
    };

    handleAdd = newValue => {
        const values = [...this.state.values, newValue];
        this.setState({ values });
        this.handleChange(values);
    };

    handleDelete = newValue => {
        const values = this.state.values.filter(v => v.value !== newValue);
        this.setState({ values });
        this.handleChange(values);
    };

    extractIds = eventOrValue => {
        const value =
            eventOrValue.target && eventOrValue.target.value
                ? eventOrValue.target.value
                : eventOrValue;
        if (Array.isArray(value)) {
            return value.map(o => o.value);
        }
        return [value];
    };

    fetchReferenceAndOptions({ input } = this.props) {
        console.log(this.props);
        this.props.crudGetMatching(
            this.props.reference,
            `${this.props.resource}@${this.props.source}`,
            this.props.pagination,
            this.props.sort,
            {}
        );
    }

    formatChoices = () => {
        const choices = [];
        Object.values(this.props.referenceRecords).forEach(value => {
            choices.push({
                text: get(value, this.props.optionText),
                value: get(value, this.props.optionValue),
            });
        });

        return choices;
    };

    render() {
        const { meta } = this.props;
        const { touched, error } = meta;

        return (
            <div></div>
            /**
            <ChipInput
                value={this.state.values}
                onBlur={this.handleBlur}
                onFocus={this.handleFocus}
                onClick={this.handleFocus}
                onRequestAdd={this.handleAdd}
                onRequestDelete={this.handleDelete}
                floatingLabelText={
                    <FieldTitle
                        label={this.props.label}
                        source={this.props.source}
                        resource={this.props.reference}
                        isRequired={false}
                    />
                }
                errorText={touched && error}
                dataSource={this.formatChoices()}
                dataSourceConfig={{ text: 'text', value: 'value' }}
                openOnFocus
            />
            **/
        );
    }
}

ReferenceObjectArrayInput.propTypes = {
    source: PropTypes.string.isRequired,
    reference: PropTypes.string.isRequired,
    label: PropTypes.string,
    basePath: PropTypes.string,
    optionValue: PropTypes.string.isRequired,
    optionText: PropTypes.string.isRequired,
    sort: PropTypes.object.isRequired,
    pagination: PropTypes.object.isRequired,
    formName: PropTypes.string.isRequired,
    referenceKey: PropTypes.string.isRequired, // The key in which to store the reference record. Ie: 'tag' -> {tag: 'id'}
    normalizedKey: PropTypes.string.isRequired,
};

const mapStateToProps = (state, props) => {
    const referenceRecords = state.admin.resources[props.reference].data;

    return {
        referenceRecords,
    };
};

const enhance = compose(
    translate,
    addField,
    connect(mapStateToProps, {
        crudGetMany: crudGetManyAction,
        crudGetMatching: crudGetMatchingAction,
        change: changeAction,
    })
);

const EnhancedReferenceObjectArrayInput = enhance(ReferenceObjectArrayInput);

EnhancedReferenceObjectArrayInput.defaultProps = {
    optionValue: 'id',
    normalizedKey: '@id',
    optionText: 'name',
    pagination: { page: 1, perPage: 500 },
    sort: { field: 'name', order: 'ASC' },
    formName: 'record-form',
};

export default EnhancedReferenceObjectArrayInput;
