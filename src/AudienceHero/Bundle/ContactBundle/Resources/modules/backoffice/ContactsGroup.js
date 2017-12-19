import React from 'react';
import NavigationRefresh from 'material-ui-icons/Refresh';
import {
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
    ListButton,
    DeleteButton,
    ReferenceArrayInput,
    SelectInput,
    Create,
} from 'react-admin';
import { CardActions } from 'material-ui/Card';
import Button from "material-ui/Button"

const cardActionStyle = {
    zIndex: 2,
    display: 'inline-block',
    float: 'right',
};

export const ContactsGroupEditTitle = record => {
    return <span>Edit {record ? `${record['name']}` : ''}</span>;
};

export const ContactsGroupEditActions = ({ basePath, data, refresh }) => (
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

export const ContactsGroupEdit = props => (
    <Edit
        {...props}
        title={<ContactsGroupEditTitle />}
        actions={<ContactsGroupEditActions />}
    >
        <SimpleForm>
            <TextInput source="name" />
            <TextInput source="description" />
        </SimpleForm>
    </Edit>
);

export const ContactsGroupList = props => (
    <List {...props}>
        <Datagrid>
            <TextField source="name" />
            <TextField source="description" />
            <DateField source="createdAt" />
            <ShowButton />
            <EditButton />
        </Datagrid>
    </List>
);

export const ContactsGroupCreate = props => (
    <Create {...props}>
        <SimpleForm>
            <TextInput source="name" />
            <TextInput source="description" />
        </SimpleForm>
    </Create>
);
