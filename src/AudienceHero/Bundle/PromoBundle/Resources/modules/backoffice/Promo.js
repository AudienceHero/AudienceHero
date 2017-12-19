import React from 'react';
import {
    SimpleForm,
    List,
    Edit,
    EditButton,
    DisabledInput,
    TextInput,
    Datagrid,
    TextField,
    EmailField,
    DateField,
    ShowButton,
    Create,
    FormTab,
    ReferenceInput,
    SelectInput,
    TabbedForm,
    LongTextInput,
    SingleFieldList,
} from 'react-admin';
import { DialogInput } from '@audiencehero-backoffice/file';
import { StatusField } from '@audiencehero-backoffice/mailing-campaign';
import {
    Actions,
    CardRow,
    ReferenceTitle,
    CardFieldRow,
    CardShow,
} from '@audiencehero-backoffice/core';
import PreviewButton from './PreviewButton';
import SendButton from './SendButton';
import BoostButton from './BoostButton';

export const PromoList = props => (
    <List {...props} sort={{ field: 'createdAt', order: 'desc' }}>
        <Datagrid>
            <StatusField
                source="mailing.status"
                label="ah.promo.field.status"
            />
            <TextField source="reference" label="ah.core.field.reference" />
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

export const PromoCreate = props => (
    <Create {...props}>
        <TabbedForm>
            <FormTab label="ah.promo.form.tab.setup">
                <LongTextInput
                    source="reference"
                    label="ah.core.input.reference"
                />
                <ReferenceInput
                    source="mailing.personEmail"
                    reference="person_emails"
                    label="ah.promo.input.fromEmail"
                    filter={{ isVerified: true }}
                    allowEmpty
                >
                    <SelectInput optionValue="@id" optionText="email" />
                </ReferenceInput>
                <TextInput
                    source="mailing.fromName"
                    label="ah.promo.input.fromName"
                />
                <TextInput
                    source="mailing.subject"
                    label="ah.promo.input.subject"
                />
                <ReferenceInput
                    source="mailing.contactsGroup"
                    reference="contacts_groups"
                    label="ah.promo.input.contactsGroup"
                    allowEmpty
                >
                    <SelectInput optionValue="@id" />
                </ReferenceInput>
                <ReferenceInput
                    source="player"
                    reference="players"
                    label="ah.promo.input.player"
                    allowEmpty
                >
                    <SelectInput optionValue="@id" optionText="title" />
                </ReferenceInput>
                <ReferenceInput
                    source="artwork"
                    reference="files"
                    label="ah.promo.input.artwork"
                    filter={{ filetype: 'image' }}
                    allowEmpty
                >
                    <DialogInput />
                </ReferenceInput>
                <ReferenceInput
                    source="download"
                    reference="files"
                    label="ah.promo.input.download"
                    filter={{ filetype: 'archive' }}
                    allowEmpty
                >
                    <DialogInput />
                </ReferenceInput>
            </FormTab>
            <FormTab label="ah.promo.form.tab.release">
                <TextInput source="name" label="ah.promo.input.name" />
                <LongTextInput
                    source="description"
                    label="ah.promo.input.description"
                />
                <TextInput source="label" label="ah.promo.input.label" />
                <TextInput source="catalog" label="ah.promo.input.catalog" />
                <TextInput source="genre" label="ah.promo.input.genre" />
                <TextInput
                    source="releaseDate"
                    label="ah.promo.input.releaseDate"
                />
            </FormTab>
        </TabbedForm>
    </Create>
);

export const PromoEdit = props => (
    <Edit {...props} title={<ReferenceTitle />}>
        <TabbedForm>
            <FormTab label="ah.promo.form.tab.setup">
                <LongTextInput
                    source="reference"
                    label="ah.core.input.reference"
                />
                <ReferenceInput
                    source="mailing.personEmail.@id"
                    reference="person_emails"
                    label="ah.promo.input.fromEmail"
                    filter={{ isVerified: true }}
                    allowEmpty
                >
                    <SelectInput optionValue="@id" optionText="email" />
                </ReferenceInput>
                <TextInput
                    source="mailing.fromName"
                    label="ah.promo.input.fromName"
                />
                <TextInput
                    source="mailing.subject"
                    label="ah.promo.input.subject"
                />
                <ReferenceInput
                    source="mailing.contactsGroup.@id"
                    reference="contacts_groups"
                    label="ah.promo.input.contactsGroup"
                    allowEmpty
                >
                    <SelectInput optionValue="@id" />
                </ReferenceInput>
                <ReferenceInput
                    source="player"
                    reference="players"
                    label="ah.promo.input.player"
                    allowEmpty
                >
                    <SelectInput optionValue="@id" optionText="title" />
                </ReferenceInput>
                <ReferenceInput
                    source="artwork"
                    reference="files"
                    label="ah.promo.input.artwork"
                    filter={{ filetype: 'image' }}
                    allowEmpty
                >
                    <DialogInput />
                </ReferenceInput>
                <ReferenceInput
                    source="download"
                    reference="files"
                    label="ah.promo.input.download"
                    filter={{ filetype: 'archive' }}
                    allowEmpty
                >
                    <DialogInput />
                </ReferenceInput>
            </FormTab>
            <FormTab label="ah.promo.form.tab.release">
                <TextInput source="name" label="ah.promo.input.name" />
                <LongTextInput
                    source="description"
                    label="ah.promo.input.description"
                />
                <TextInput source="label" label="ah.promo.input.label" />
                <TextInput source="catalog" label="ah.promo.input.catalog" />
                <TextInput source="genre" label="ah.promo.input.genre" />
                <TextInput
                    source="releaseDate"
                    label="ah.promo.input.releaseDate"
                />
            </FormTab>
        </TabbedForm>
    </Edit>
);
