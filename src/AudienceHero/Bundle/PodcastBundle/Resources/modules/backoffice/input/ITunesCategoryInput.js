import React, { Component } from 'react';
import PropTypes from 'prop-types';
import {Select} from 'material-ui';
import { translate, FieldTitle } from 'react-admin';

const iTunesCategories = {
    Arts: [
        'Design',
        'Fashion & Beauty',
        'Food',
        'Literature',
        'Performing Arts',
        'Visual Arts',
    ],
    Business: [
        'Business News',
        'Careers',
        'Investing',
        'Management & Marketing',
        'Shopping',
    ],
    Comedy: [],
    Education: [
        'Educational Technology',
        'Higher Education',
        'K-12',
        'Language Courses',
        'Training',
    ],
    'Games & Hobbies': [
        'Automotive',
        'Aviation',
        'Hobbies',
        'Other Games',
        'Video Games',
    ],
    'Government & Organizations': [
        'Local',
        'National',
        'Non-Profit',
        'Regional',
    ],
    Health: [
        'Alternative Health',
        'Fitness & Nutrition',
        'Self-Help',
        'Sexuality',
    ],
    'Kids & Family': [],
    Music: [],
    'News & Politics': [],
    'Religion & Spirituality': [
        'Buddhism',
        'Christianity',
        'Hinduism',
        'Islam',
        'Judaism',
        'Other',
        'Spirituality',
    ],
    'Science & Medicine': ['Medicine', 'Natural Sciences', 'Social Sciences'],
    'Society & Culture': [
        'History',
        'Personal Journals',
        'Philosophy',
        'Places & Travel',
    ],
    'Sports & Recreation': [
        'Amateur',
        'College & High School',
        'Outdoor',
        'Professional',
    ],
    Technology: ['Gadgets', 'Tech News', 'Podcasting', 'Software How-To'],
    'TV & Film': [],
};

export class ITunesCategoryInput extends Component {
    /*
     * Using state to bypass a redux-form comparison but which prevents re-rendering
     * @see https://github.com/erikras/redux-form/issues/2456
     */
    state = {
        value: this.props.input.value,
    };

    handleChange = (event, index, value) => {
        this.props.input.onChange(value);
        this.setState({ value });
    };

    render() {
        const {
            allowEmpty,
            elStyle,
            isRequired,
            translate,
            choices,
            label,
            meta,
            options,
            resource,
            source,
        } = this.props;
        if (typeof meta === 'undefined') {
            throw new Error(
                "The ITunesCategoryInput component wasn't called within a redux-form <Field>. Did you decorate it and forget to add the addField prop to your component? See https://marmelab.com/react-admin/Inputs.html#writing-your-own-input-component for details."
            );
        }
        const { touched, error } = meta;

        const menuItems = [];
        {
            Object.keys(choices).map(category => {
                const childs = [];
                menuItems.push(
                    <MenuItem
                        key={category}
                        value={category}
                        primaryText={translate(category)}
                    />
                );
                for (let subcategory of choices[category]) {
                    menuItems.push(
                        <MenuItem
                            key={subcategory}
                            value={subcategory}
                            primaryText={`– ${translate(subcategory)}`}
                        />
                    );
                }
            });
        }

        return (
            <SelectField
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
                {menuItems}
            </SelectField>
        );
    }
}

ITunesCategoryInput.propTypes = {
    addField: PropTypes.bool.isRequired,
    allowEmpty: PropTypes.bool.isRequired,
    choices: PropTypes.object,
    elStyle: PropTypes.object,
    input: PropTypes.object,
    isRequired: PropTypes.bool,
    label: PropTypes.string,
    meta: PropTypes.object,
    options: PropTypes.object,
    resource: PropTypes.string,
    source: PropTypes.string,
    locale: PropTypes.string.isRequired,
};

ITunesCategoryInput.defaultProps = {
    addField: true,
    allowEmpty: false,
    options: {},
    choices: iTunesCategories,
};

export default translate(ITunesCategoryInput);
