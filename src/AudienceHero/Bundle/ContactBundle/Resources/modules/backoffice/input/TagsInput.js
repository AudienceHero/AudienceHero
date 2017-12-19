import React from 'react';
import PropTypes from 'prop-types';
import {
    translate,
    crudGetMany as crudGetManyAction,
    crudGetMatching as crudGetMatchingAction,
    SelectArrayInput,
    FieldTitle,
} from 'react-admin';
import { connect } from 'react-redux';
import compose from 'recompose/compose';
import ChipInput from 'material-ui-chip-input';
import { change as changeAction } from 'redux-form';
import get from 'lodash.get';

export class TagsInput extends React.Component {
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
        const existing = get(nextProps, 'record.tags');
        if (!existing) {
            return;
        }

        const values = this.state.values;
        for (const contactTag of existing) {
            const tagId = get(contactTag, 'tag.id');
            if (-1 === values.findIndex(element => element === tagId)) {
                values.push({ text: contactTag.tag.name, value: tagId });
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
        const existing = get(this.props, 'record.tags');
        const available = this.props.tags;

        tagLoop: for (const tagId of extracted) {
            // Check if the value can be extracted from 'existing' tags
            // If not, extract value from the available tags
            for (const contactTag of existing) {
                if (tagId === get(contactTag, 'tag.id')) {
                    normalized.push(contactTag);
                    continue tagLoop;
                }
            }

            const availableTagIRI = get(available, `${tagId}.@id`);
            if (availableTagIRI) {
                normalized.push({ tag: availableTagIRI });
            }
        }

        return normalized;
    };

    handleChange = eventOrValue => {
        const extracted = this.extractIds(eventOrValue);
        const normalized = this.normalize(extracted);
        this.setState({ normalized });
        this.props.change('record-form', 'tags', normalized);
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
        const ids = input.value;
        if (ids) {
            if (!Array.isArray(ids)) {
                throw Error(
                    'The value of ReferenceArrayInput should be an array'
                );
            }
            // this.props.crudGetMany('tags', ids);
        }

        this.props.crudGetMatching(
            'tags',
            'contacts@tags',
            { page: 1, perPage: 500 },
            { field: 'name', order: 'ASC' },
            {}
        );
    }

    formatChoices = () => {
        const choices = [];
        Object.values(this.props.tags).forEach(value => {
            choices.push({ text: value.name, value: value['id'] });
        });

        return choices;
    };

    render() {
        const { label, meta } = this.props;
        const { touched, error } = meta;
        // {...input}
        console.log('values', this.state.values);
        return (
            <ChipInput
                value={this.state.values}
                onBlur={this.handleBlur}
                onFocus={this.handleFocus}
                onClick={this.handleFocus}
                onRequestAdd={this.handleAdd}
                onRequestDelete={this.handleDelete}
                // onUpdateInput={setFilter}
                floatingLabelText={
                    <FieldTitle
                        label={this.props.label}
                        source={this.props.source}
                        resource={'tags'}
                        isRequired={false}
                    />
                }
                errorText={touched && error}
                dataSource={this.formatChoices()}
                dataSourceConfig={{ text: 'text', value: 'value' }}
                openOnFocus
            />
        );
    }
}

TagsInput.propTypes = {
    source: PropTypes.string.isRequired,
    label: PropTypes.string,
    basePath: PropTypes.string,
    tags: PropTypes.object.isRequired,
};

const mapStateToProps = (state, props) => {
    const referenceIds = props.input.value || [];
    const tags = state.admin.resources['tags'].data;

    return {
        tags,
        referenceRecords: referenceIds.reduce((references, referenceId) => {
            if (tags[referenceId]) {
                references.push(tags[referenceId]);
            }

            return references;
        }, []),
    };
};

const enhance = compose(
    translate,
    connect(mapStateToProps, {
        crudGetMany: crudGetManyAction,
        crudGetMatching: crudGetMatchingAction,
        change: changeAction,
    })
);

const EnhancedTagsInput = enhance(TagsInput);

EnhancedTagsInput.defaultProps = {
    addField: true,
    source: 'tags',
};

export default EnhancedTagsInput;
