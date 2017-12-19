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
    Show,
    SimpleForm,
    TextField,
    TextInput,
} from 'react-admin';
import PersonEmailSendVerificationMailButton from './PersonEmailSendVerificationMailButton';

export const PersonEmailList = props => (
    <List {...props}>
        <Datagrid>
            <TextField source="email" />
            <BooleanField source="isVerified" />
            <DateField source="createdAt" />
            <PersonEmailSendVerificationMailButton />
            <DeleteButton />
        </Datagrid>
    </List>
);

export const PersonEmailCreate = props => (
    <Create {...props}>
        <SimpleForm>
            <TextInput source="email" />
        </SimpleForm>
    </Create>
);

export const PersonEmailVerify = props => <Show />;
