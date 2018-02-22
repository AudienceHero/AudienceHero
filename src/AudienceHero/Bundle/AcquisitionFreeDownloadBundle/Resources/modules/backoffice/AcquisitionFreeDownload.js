import React from 'react';
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
    DateInput as DateTimeInput
} from 'react-admin';
import { DependentInput } from 'ra-dependent-input';
import {
    PrivacyField,
    PrivacyInputChoices,
} from '@audiencehero-backoffice/core';
import { DialogInput } from '@audiencehero-backoffice/file';
import { translate } from 'react-admin';

export const AcquisitionFreeDownloadEdit = props => (
    <Edit {...props}>
        <TabbedForm>
            <FormTab label="ah.afd.formtab.setup">
                <LongTextInput
                    source="reference"
                    label="ah.core.input.reference"
                />
                <TextInput source="title" label="ah.afd.input.title" />
                <LongTextInput
                    source="description"
                    label="ah.afd.input.description"
                />
                <ReferenceInput
                    source="player"
                    reference="players"
                    label="ah.afd.input.player"
                    allowEmpty
                >
                    <SelectInput optionValue="@id" optionText="title" />
                </ReferenceInput>
                <ReferenceInput
                    source="artwork"
                    reference="files"
                    label="ah.afd.input.artwork"
                    filter={{ filetype: 'image' }}
                    allowEmpty
                >
                    <DialogInput />
                </ReferenceInput>
                <ReferenceInput
                    source="download"
                    reference="files"
                    label="ah.afd.input.download"
                    filter={{ filetype: 'archive' }}
                    allowEmpty
                >
                    <DialogInput />
                </ReferenceInput>
                <SelectInput
                    source="privacy"
                    label="ah.afd.input.privacy"
                    choices={PrivacyInputChoices}
                />
                <DependentInput dependsOn="privacy" value="scheduled">
                    <DateTimeInput source="scheduledAt" />
                </DependentInput>
            </FormTab>
            <FormTab label="ah.afd.formtab.acquisition">
                <ReferenceInput
                    allowEmpty={true}
                    source="contactsGroupForm.@id"
                    reference="contacts_group_forms"
                    label="ah.afd.input.contacts_group_form"
                >
                    <SelectInput optionValue="@id" />
                </ReferenceInput>
                <TextInput source="fromName" type="email" />
                <ReferenceInput
                    allowEmpty={true}
                    source="personEmail.@id"
                    reference="person_emails"
                    label="ah.afd.input.personEmail"
                    filter={{ isVerified: true }}
                >
                    <SelectInput optionValue="@id" optionText="email" />
                </ReferenceInput>
            </FormTab>
        </TabbedForm>
    </Edit>
);

export const AcquisitionFreeDownloadCreate = props => (
    <Create {...props}>
        <TabbedForm>
            <FormTab label="ah.afd.formtab.setup">
                <LongTextInput
                    source="reference"
                    label="ah.core.input.reference"
                />
                <TextInput source="title" label="ah.afd.input.title" />
                <LongTextInput
                    source="description"
                    label="ah.afd.input.description"
                />
                <ReferenceInput
                    source="player"
                    reference="players"
                    label="ah.afd.input.player"
                    allowEmpty
                >
                    <SelectInput optionValue="@id" optionText="title" />
                </ReferenceInput>
                <ReferenceInput
                    source="artwork"
                    reference="files"
                    label="ah.afd.input.artwork"
                    filter={{ filetype: 'image' }}
                    allowEmpty
                >
                    <DialogInput />
                </ReferenceInput>
                <ReferenceInput
                    source="download"
                    reference="files"
                    label="ah.afd.input.download"
                    filter={{ filetype: 'archive' }}
                    allowEmpty
                >
                    <DialogInput />
                </ReferenceInput>
                <SelectInput
                    source="privacy"
                    label="ah.afd.input.privacy"
                    choices={PrivacyInputChoices}
                />
                <DependentInput dependsOn="privacy" value="scheduled">
                    <DateTimeInput source="scheduledAt" />
                </DependentInput>
            </FormTab>
            <FormTab label="ah.afd.formtab.acquisition">
                <ReferenceInput
                    allowEmpty={true}
                    source="contactsGroupForm"
                    reference="contacts_group_forms"
                    label="ah.afd.input.contacts_group_form"
                >
                    <SelectInput optionValue="@id" />
                </ReferenceInput>
                <TextInput source="fromName" type="email" />
                <ReferenceInput
                    allowEmpty={true}
                    source="personEmail"
                    reference="person_emails"
                    label="ah.afd.input.personEmail"
                    filter={{ isVerified: true }}
                >
                    <SelectInput optionValue="@id" optionText="email" />
                </ReferenceInput>
            </FormTab>
        </TabbedForm>
    </Create>
);

export const AcquisitionFreeDownloadList = props => (
    <List {...props}>
        <Datagrid>
            <PrivacyField
                source="privacy"
                label="ah.core.field.privacy"
                sortable={false}
            />
            <TextField
                source="reference"
                label="ah.core.field.reference"
                sortable={false}
            />
            <DateField
                source="createdAt"
                label="ah.core.field.createdAt"
                sortable={true}
            />
            <ShowButton />
            <EditButton />
        </Datagrid>
    </List>
);
