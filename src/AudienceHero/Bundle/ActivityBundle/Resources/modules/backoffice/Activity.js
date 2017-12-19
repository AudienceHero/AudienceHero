import React from 'react';
import PropTypes from 'prop-types';
import {
    Create,
    List,
    Datagrid,
    TextField,
    LongTextInput,
    ReferenceInput,
    EmailField,
    DateField,
    EditButton,
    ShowButton,
    TabbedForm,
    SelectInput,
    FormTab,
    Edit,
    TextInput,
} from 'react-admin';

export const ActivityList = ({ props }) => (
    <List>
        <TextField source="type" />
    </List>
);
