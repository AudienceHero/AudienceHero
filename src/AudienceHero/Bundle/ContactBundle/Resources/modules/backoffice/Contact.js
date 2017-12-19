import React from 'react';
import {
    Filter,
    List,
    Datagrid,
    TextField,
    EmailField,
    ShowButton,
    EditButton,
    DateField,
    Edit,
    SimpleForm,
    BooleanInput,
    TextInput,
    LongTextInput,
    ReferenceManyField,
    SingleFieldList,
    ChipField,
    ReferenceArrayField,
    SelectInput,
    ReferenceInput,
    Create,
    ReferenceArrayInput,
    SelectArrayInput,
} from 'react-admin';
import get from 'lodash.get';

import Chip from 'material-ui/Chip';
import { CountryInput } from '@audiencehero-backoffice/core';
import ReferenceObjectArrayInput from './input/ReferenceObjectArrayInput';

export const ContactCreate = props => (
    <Create {...props}>
        <SimpleForm>
            <TextInput source="email" label="ah.contact.input.email" />
            <TextInput source="name" label="ah.contact.input.name" />
            <BooleanInput
                source="isCompany"
                label="ah.contact.input.isCompany"
            />
            <ReferenceObjectArrayInput
                label="ah.contact.input.tags"
                source="tags"
                reference="tags"
                referenceKey="tag"
            />
            <ReferenceObjectArrayInput
                label="ah.contact.input.groups"
                source="groups"
                reference="contacts_groups"
                referenceKey="group"
            />
            <TextInput
                source="salutationName"
                label="ah.contact.input.salutationName"
            />
            <TextInput source="company" label="ah.contact.input.company" />
            <TextInput source="phone" label="ah.contact.input.phone" />
            <CountryInput source="country" label="ah.contact.input.country" />
            <TextInput source="city" label="ah.contact.input.city" />
            <TextInput
                source="postalCode"
                label="ah.contact.input.postalCode"
            />
            <TextInput source="address" label="ah.contact.input.address" />
            <LongTextInput source="notes" label="ah.contact.input.notes" />
        </SimpleForm>
    </Create>
);

export const ContactTitle = ({ record }) => (
    <span>
        {get(record, 'name') ? get(record, 'name') : get(record, 'email')}
    </span>
);

/*<TagsInput source="tags" />*/
export const ContactEdit = props => (
    <Edit {...props} title={<ContactTitle />}>
        <SimpleForm>
            <TextInput source="email" label="ah.contact.input.email" />
            <TextInput source="name" label="ah.contact.input.name" />
            <ReferenceObjectArrayInput
                label="ah.contact.input.tags"
                source="tags"
                reference="tags"
                referenceKey="tag"
            />
            <ReferenceObjectArrayInput
                label="ah.contact.input.groups"
                source="groups"
                reference="contacts_groups"
                referenceKey="group"
            />
            <BooleanInput
                source="isCompany"
                label="ah.contact.input.isCompany"
            />
            <TextInput
                source="salutationName"
                label="ah.contact.input.salutationName"
            />
            <TextInput source="company" label="ah.contact.input.company" />
            <TextInput source="phone" label="ah.contact.input.phone" />
            <CountryInput source="country" label="ah.contact.input.country" />
            <TextInput source="city" label="ah.contact.input.city" />
            <TextInput
                source="postalCode"
                label="ah.contact.input.postalCode"
            />
            <TextInput source="address" label="ah.contact.input.address" />
            <LongTextInput source="notes" label="ah.contact.input.notes" />
        </SimpleForm>
    </Edit>
);

const GroupField = ({ source, record = {} }) => {
    const groups = get(record, source);
    if (!Array.isArray(groups)) {
        return false;
    }

    return (
        <div>
            {groups.map((cgc, index) => {
                return (
                    <Chip key={index} style={{ margin: 4 }}>
                        {get(cgc, 'group.name')}
                    </Chip>
                );
            })}
        </div>
    );
};

const TagField = ({ source, record = {} }) => {
    const groups = get(record, source);
    if (!Array.isArray(groups)) {
        return false;
    }

    return (
        <div>
            {groups.map((cgc, index) => {
                return (
                    <Chip key={index} style={{ margin: 4 }}>
                        {get(cgc, 'tag.name')}
                    </Chip>
                );
            })}
        </div>
    );
};

export const ContactFilter = props => (
    <Filter {...props}>
        <TextInput label="ah.core.filter.search" source="q" alwaysOn />
        <ReferenceInput
            label="ah.contact.filter.group"
            source="groups.group.name"
            reference="contacts_groups"
            allowEmpty
        >
            <SelectInput optionValue="name" optionText="name" />
        </ReferenceInput>
        <ReferenceInput
            label="ah.contact.filter.tag"
            source="tags.tag.name"
            reference="tags"
            allowEmpty
        >
            <SelectInput optionValue="name" optionText="name" />
        </ReferenceInput>
        <CountryInput label="ah.contact.filter.country" source="country" />
    </Filter>
);

export const ContactList = props => (
    <List perPage={30} filters={<ContactFilter />} {...props}>
        <Datagrid>
            <TextField source="name" label="ah.contact.field.name" />
            <EmailField source="email" label="ah.contact.field.email" />
            <TextField
                source="phone"
                label="ah.contact.field.phone"
                sortable={false}
            />
            <GroupField
                source="groups"
                label="ah.contact.field.groups"
                sortable={false}
            />
            <TagField
                source="tags"
                label="ah.contact.field.tags"
                sortable={false}
            />
            <DateField source="createdAt" label="ah.core.field.createdAt" />
            <ShowButton />
            <EditButton />
        </Datagrid>
    </List>
);
