import React from 'react';
import {
    BooleanField,
    Create,
    Datagrid,
    DateField,
    DeleteButton,
    DisabledInput,
    Edit,
    EditButton,
    EmailField,
    List,
    LongTextInput,
    Show,
    SimpleForm,
    TextField,
    TextInput,
} from 'react-admin';

export const TagTitle = ({ record }) => (
    <span>{record ? record.name : ''}</span>
);

export const TagList = props => (
    <List {...props} perPage={30} sort={{ field: 'createdAt', order: 'DESC' }}>
        <Datagrid>
            <TextField
                source="name"
                label="ah.core.field.tag.name"
                sortable={false}
            />
            <TextField
                source="description"
                label="ah.core.field.tag.description"
                sortable={false}
            />
            <DateField source="createdAt" label="ah.core.field.createdAt" />
            <EditButton />
        </Datagrid>
    </List>
);

export const TagCreate = props => (
    <Create {...props}>
        <SimpleForm>
            <TextInput source="name" label="ah.core.input.tag.name" />
            <LongTextInput
                source="description"
                label="ah.core.input.tag.description"
            />
        </SimpleForm>
    </Create>
);

export const TagEdit = props => (
    <Edit {...props} title={<TagTitle />}>
        <SimpleForm>
            <TextInput source="name" label="ah.core.input.tag.name" />
            <LongTextInput
                source="description"
                label="ah.core.input.tag.description"
            />
        </SimpleForm>
    </Edit>
);
