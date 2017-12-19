import React from 'react';
import {
    Create,
    ChipField,
    List,
    Datagrid,
    TextField,
    EmailField,
    ShowButton,
    EditButton,
    DateField,
    Edit,
    FormTab,
    ReferenceField,
    ReferenceArrayInput,
    ReferenceInput,
    SelectInput,
    SimpleForm,
    LongTextInput,
    TabbedForm,
    TextInput,
    ListButton,
    DeleteButton,
    SaveButton,
    Toolbar,
    Show,
} from 'react-admin';
import NavigationRefresh from 'material-ui-icons/Refresh';
import { CardActions } from 'material-ui/Card';
import Button from "material-ui/Button"
import StatusField from './StatusField';
import SendButton from './SendButton';
import PreviewButton from './PreviewButton';
import BoostButton from './BoostButton';
import { Row } from 'react-flexbox-grid';
import {
    CardProgress,
    CardRow,
    CardStat,
    Actions,
    CardShow,
} from '@audiencehero-backoffice/core';
import { DialogInput } from '@audiencehero-backoffice/file';
import MailboxIcon from 'material-ui-icons/Inbox';
import ClearIcon from 'material-ui-icons/Clear';
import DraftsIcon from 'material-ui-icons/Drafts';
import LinkIcon from 'material-ui-icons/Link';

const cardActionStyle = {
    zIndex: 2,
    display: 'inline-block',
    float: 'right',
};

export const MailingTitle = ({ record }) => {
    return <span>File {record ? `"${record.subject}"` : ''}</span>;
};

export const EditActions = ({ basePath, data, refresh }) => (
    <CardActions style={cardActionStyle}>
        <ShowButton basePath={basePath} record={data} />
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

export const MailingCreate = props => (
    <Create title="Create" {...props}>
        <TabbedForm>
            <FormTab label="setup">
                <LongTextInput
                    source="reference"
                    label="ah.core.input.reference"
                />
                <ReferenceInput
                    allowEmpty={true}
                    source="contactsGroup"
                    reference="contacts_groups"
                    label="Recipients"
                >
                    <SelectInput optionValue="@id" />
                </ReferenceInput>
                <ReferenceInput
                    allowEmpty={true}
                    source="personEmail"
                    reference="person_emails"
                    label="From Email"
                    filter={{ isVerified: true }}
                >
                    <SelectInput optionValue="@id" optionText="email" />
                </ReferenceInput>
                <TextInput source="fromName" />
            </FormTab>
            <FormTab label="compose">
                <TextInput source="subject" />
                <ReferenceInput
                    source="artwork"
                    reference="files"
                    label="Artwork"
                    allowEmpty
                >
                    <DialogInput />
                </ReferenceInput>
                <LongTextInput source="body" />
            </FormTab>
        </TabbedForm>
    </Create>
);

export const MailingEdit = props => (
    <Edit title="Edit me" actions={<EditActions />} {...props}>
        <TabbedForm>
            <FormTab label="setup">
                <LongTextInput
                    source="reference"
                    label="ah.core.input.reference"
                />
                <ReferenceInput
                    allowEmpty={true}
                    source="contactsGroup"
                    reference="contacts_groups"
                    label="Recipients"
                >
                    <SelectInput optionValue="@id" />
                </ReferenceInput>
                <ReferenceInput
                    allowEmpty={true}
                    source="personEmail"
                    reference="person_emails"
                    label="From Email"
                    filter={{ isVerified: true }}
                >
                    <SelectInput optionValue="@id" optionText="email" />
                </ReferenceInput>
                <TextInput source="fromName" />
            </FormTab>
            <FormTab label="compose">
                <TextInput source="subject" />
                <ReferenceInput
                    source="artwork"
                    reference="files"
                    label="Artwork"
                    allowEmpty
                >
                    <DialogInput />
                </ReferenceInput>
                <LongTextInput source="body" />
            </FormTab>
        </TabbedForm>
    </Edit>
);

export const MailingList = props => (
    <List
        {...props}
        filter={{ isInternal: false }}
        sort={{ field: 'createdAt', order: 'DESC' }}
    >
        <Datagrid>
            <StatusField source="status" label="ah.mailing.field.status" />
            <TextField source="reference" label="ah.mailing.field.reference" />
            <TextField source="subject" label="ah.mailing.field.subject" />
            <ReferenceField
                label="ah.mailing.field.contactsGroup"
                source="contactsGroup.id"
                linkType="show"
                reference="contacts_groups"
            >
                <ChipField source="name" />
            </ReferenceField>
            <DateField source="createdAt" label="ah.core.field.createdAt" />
            <ShowButton />
            <EditButton />
            <Actions>
                <PreviewButton />
                <SendButton />
                <BoostButton />
            </Actions>
        </Datagrid>
    </List>
);

export const MailingShowTitle = ({ record }) => {
    return <span>{record ? `"${record.subject}"` : ''}</span>;
};

export const MailingShow = props => (
    <CardShow {...props} title={<MailingShowTitle />}>
        <CardRow>
            <CardProgress
                xs={12}
                sm={12}
                md={3}
                lg={3}
                source="rateDelivery"
                label="ah.mailing.rate.delivery"
            />
            <CardProgress
                xs={12}
                sm={12}
                md={3}
                lg={3}
                source="rateOpen"
                label="ah.mailing.rate.open"
            />
            <CardProgress
                xs={12}
                sm={12}
                md={3}
                lg={3}
                source="rateClick"
                label="ah.mailing.rate.click"
            />
            <CardProgress
                xs={12}
                sm={12}
                md={3}
                lg={3}
                source="rateClickByUniqueOpen"
                label="ah.mailing.rate.click_by_unique_open"
            />
        </CardRow>
        <CardRow>
            <CardStat
                source="countDelivered"
                label="ah.mailing.stat.delivered"
                icon={<MailboxIcon />}
            />
            <CardStat
                source="countNonDelivered"
                label="ah.mailing.stat.non_delivered"
                icon={<ClearIcon />}
            />
        </CardRow>
        <CardRow>
            <CardStat
                source="countTotalOpens"
                label="ah.mailing.stat.total_opens"
                icon={<DraftsIcon />}
            />
            <CardStat
                source="countUniqueOpen"
                label="ah.mailing.stat.unique_opens"
                icon={<DraftsIcon />}
            />
        </CardRow>
        <CardRow>
            <CardStat
                source="countTotalClicks"
                label="ah.mailing.stat.total_clicks"
                icon={<LinkIcon />}
            />
            <CardStat
                source="countUniqueClick"
                label="ah.mailing.stat.unique_clicks"
                icon={<LinkIcon />}
            />
        </CardRow>
    </CardShow>
);
