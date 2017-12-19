import React from 'react';
import NavigationRefresh from 'material-ui-icons/Refresh';
import {
    List,
    Datagrid,
    TextField,
    ChipField,
    EmailField,
    ShowButton,
    EditButton,
    DateField,
    Edit,
    DeleteButton,
    SimpleForm,
    BooleanInput,
    TextInput,
    LongTextInput,
    ListButton,
    ReferenceArrayInput,
    SelectInput,
    Show,
    ReferenceField,
    Create,
    ReferenceInput,
} from 'react-admin';
import { CardActions } from 'material-ui/Card';
import Button from "material-ui/Button"
import { DialogInput } from '@audiencehero-backoffice/file';
import { CardShow } from '@audiencehero-backoffice/core';
import CardShare from './CardShare';

const cardActionStyle = {
    zIndex: 2,
    display: 'inline-block',
    float: 'right',
};

export const ContactsGroupFormEditTitle = record => {
    return <span>Edit {record ? `${record['name']}` : ''}</span>;
};

export const ContactsGroupFormEditActions = ({ basePath, data, refresh }) => (
    <CardActions style={cardActionStyle}>
        <EditButton basePath={basePath} record={data} />
        <ListButton basePath={basePath} />
        <DeleteButton basePath={basePath} record={data} />
        <Button
            primary
            label="Refresh"
            onTouchTap={refresh}
            icon={<NavigationRefresh />}
        />
    </CardActions>
);

export const ContactsGroupFormEdit = props => (
    <Edit
        {...props}
        title={<ContactsGroupFormEditTitle />}
        actions={<ContactsGroupFormEditActions />}
    >
        <SimpleForm>
            <TextInput source="name" />
            <TextInput source="description" />

            <ReferenceInput
                allowEmpty={true}
                source="contactsGroup.@id"
                reference="contacts_groups"
                label="Group"
            >
                <SelectInput optionValue="@id" optionText="name" />
            </ReferenceInput>

            {/*
            <ReferenceInput allowEmpty={true} source="image" reference="files" label="Illustration">
                <DialogInput />
            </ReferenceInput>
            */}

            <BooleanInput source="askName" label="Ask for Name" />
            <BooleanInput source="askCity" label="Ask for City" />
            <BooleanInput source="askEmail" label="Ask for Email" />
            <BooleanInput source="askCountry" label="Ask for Country" />
            <BooleanInput source="askPhone" label="Ask for Phone" />
            <BooleanInput
                source="displayQRCode"
                label="Display QR Code on offline form?"
            />
        </SimpleForm>
    </Edit>
);

export const ContactsGroupFormCreate = props => (
    <Create {...props}>
        <SimpleForm>
            <TextInput source="name" />
            <TextInput source="description" />

            <ReferenceInput
                allowEmpty={true}
                source="contactsGroup"
                reference="contacts_groups"
                label="Group"
            >
                <SelectInput optionValue="@id" />
            </ReferenceInput>

            <ReferenceInput
                allowEmpty={true}
                source="image"
                reference="files"
                label="Illustration"
            >
                <DialogInput />
            </ReferenceInput>

            <BooleanInput source="askName" label="Ask for Name" />
            <BooleanInput source="askCity" label="Ask for City" />
            <BooleanInput source="askEmail" label="Ask for Email" />
            <BooleanInput source="askCountry" label="Ask for Country" />
            <BooleanInput source="askPhone" label="Ask for Phone" />
            <BooleanInput
                source="displayQRCode"
                label="Display QR Code on offline form?"
            />
        </SimpleForm>
    </Create>
);

export const ContactsGroupFormList = props => (
    <List {...props}>
        <Datagrid>
            <TextField source="name" />
            <TextField source="description" />
            <ReferenceField
                label="Group"
                source="contactsGroup.id"
                linkType="show"
                reference="contacts_groups"
            >
                <ChipField source="name" />
            </ReferenceField>
            <DateField source="createdAt" />
            <ShowButton />
            <EditButton />
        </Datagrid>
    </List>
);

export const ContactsGroupFormShowActions = ({ basePath, data, refresh }) => (
    <CardActions style={cardActionStyle}>
        <EditButton basePath={basePath} record={data} />
        <ListButton basePath={basePath} />
        <DeleteButton basePath={basePath} record={data} />
        <Button
            primary
            label="Refresh"
            onClick={refresh}
            icon={<NavigationRefresh />}
        />
    </CardActions>
);

export const ContactsGroupFormShow = props => (
    <CardShow {...props} actions={<ContactsGroupFormShowActions />}>
        <CardShare />
    </CardShow>
);
