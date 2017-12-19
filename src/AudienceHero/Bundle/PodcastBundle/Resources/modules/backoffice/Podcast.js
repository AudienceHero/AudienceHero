import React from 'react';
import {
    SimpleForm,
    List,
    Create,
    TabbedForm,
    Edit,
    EditButton,
    BooleanInput,
    LongTextInput,
    FormTab,
    ReferenceInput,
    EmailInput,
    SelectInput,
    ReferenceField,
    DisabledInput,
    TextInput,
    Datagrid,
    TextField,
    EmailField,
    DateField,
    ShowButton,
} from 'react-admin';
import {
    PrivacyField,
    PrivacyInputChoices,
    LanguageInput,
} from '@audiencehero-backoffice/core';
import { DialogInput } from '@audiencehero-backoffice/file';
import ITunesCategoryInput from './input/ITunesCategoryInput';
import { DependentInput } from 'ra-dependent-input';
import DateTimeInput from 'aor-datetime-input';

export const PodcastChannelList = props => (
    <List {...props}>
        <Datagrid>
            <PrivacyField
                source="privacy"
                label="ah.core.field.privacy"
                sortable={false}
            />
            <TextField
                source="title"
                label="ah.podcast.channel.field.title"
                sortable={false}
            />
            <DateField source="createdAt" label="ah.core.field.createdAt" />
            <ShowButton />
            <EditButton />
        </Datagrid>
    </List>
);

export const PodcastChannelCreate = props => (
    <Create {...props}>
        <TabbedForm>
            <FormTab label="ah.podcast.channel.formtab.setup">
                <TextInput
                    source="title"
                    label="ah.podcast.channel.input.title"
                />
                <TextInput
                    source="subtitle"
                    label="ah.podcast.channel.input.subtitle"
                />
                <LongTextInput
                    source="description"
                    label="ah.podcast.channel.input.description"
                />
                <ReferenceInput
                    source="artwork"
                    reference="files"
                    label="ah.podcast.channel.input.artwork"
                    allowEmpty
                    filter={{ filetype: 'image' }}
                >
                    <DialogInput />
                </ReferenceInput>
                <LanguageInput
                    source="language"
                    label="ah.podcast.channel.input.language"
                />
                <TextInput
                    source="author"
                    label="ah.podcast.channel.input.author"
                />
                <TextInput
                    source="copyright"
                    label="ah.podcast.channel.input.copyright"
                />
                <SelectInput
                    source="privacy"
                    label="ah.core.input.privacy"
                    choices={PrivacyInputChoices}
                />
                <DependentInput dependsOn="privacy" value="scheduled">
                    <DateTimeInput source="scheduledAt" />
                </DependentInput>
            </FormTab>
            <FormTab label="ah.podcast.channel.formtab.itunes">
                <ITunesCategoryInput
                    source="category"
                    label="ah.podcast.channel.input.itunes.category"
                />
                <TextInput
                    source="itunesOwnerEmail"
                    type="email"
                    label="ah.podcast.channel.input.itunes.owner_email"
                />
                <TextInput
                    source="itunesOwnerName"
                    label="ah.podcast.channel.input.itunes.owner_name"
                />
                <BooleanInput
                    source="itunesBlock"
                    label="ah.podcast.channel.input.itunes.block"
                />
                <BooleanInput
                    source="isExplicit"
                    label="ah.podcast.channel.input.itunes.is_explicit"
                />
                <BooleanInput
                    source="isComplete"
                    label="ah.podcast.channel.input.itunes.is_complete"
                />
            </FormTab>
            <FormTab label="ah.podcast.channel.formtab.acquisition">
                <ReferenceInput
                    source="contactsGroupsForm"
                    reference="contacts_group_forms"
                    label="ah.podcast.channel.input.contacts_group_form"
                    allowEmpty
                >
                    <SelectInput optionValue="@id" />
                </ReferenceInput>
            </FormTab>
        </TabbedForm>
    </Create>
);

export const PodcastChannelEdit = props => (
    <Edit {...props}>
        <TabbedForm>
            <FormTab label="ah.podcast.channel.formtab.setup">
                <TextInput
                    source="title"
                    label="ah.podcast.channel.input.title"
                />
                <TextInput
                    source="subtitle"
                    label="ah.podcast.channel.input.subtitle"
                />
                <LongTextInput
                    source="description"
                    label="ah.podcast.channel.input.description"
                />
                <ReferenceInput
                    source="artwork"
                    reference="files"
                    label="ah.podcast.channel.input.artwork"
                    allowEmpty
                >
                    <DialogInput />
                </ReferenceInput>
                <TextInput
                    source="language"
                    label="ah.podcast.channel.input.language"
                />
                <TextInput
                    source="author"
                    label="ah.podcast.channel.input.author"
                />
                <TextInput
                    source="copyright"
                    label="ah.podcast.channel.input.copyright"
                />
                <SelectInput
                    source="privacy"
                    label="ah.core.input.privacy"
                    choices={PrivacyInputChoices}
                />
                <DependentInput dependsOn="privacy" value="scheduled">
                    <DateTimeInput source="scheduledAt" />
                </DependentInput>
            </FormTab>
            <FormTab label="ah.podcast.channel.formtab.itunes">
                <TextInput
                    source="category"
                    label="ah.podcast.channel.input.itunes.category"
                />
                <TextInput
                    source="itunesOwnerEmail"
                    type="email"
                    label="ah.podcast.channel.input.itunes.owner_email"
                />
                <TextInput
                    source="itunesOwnerName"
                    label="ah.podcast.channel.input.itunes.owner_name"
                />
                <BooleanInput
                    source="itunesBlock"
                    label="ah.podcast.channel.input.itunes.block"
                />
                <BooleanInput
                    source="isExplicit"
                    label="ah.podcast.channel.input.itunes.is_explicit"
                />
                <BooleanInput
                    source="isComplete"
                    label="ah.podcast.channel.input.itunes.is_complete"
                />
            </FormTab>
            <FormTab label="ah.podcast.channel.formtab.acquisition">
                <ReferenceInput
                    source="contactsGroupForm"
                    reference="contacts_group_forms"
                    label="ah.podcast.channel.input.contacts_group_form"
                    allowEmpty
                >
                    <SelectInput optionValue="@id" />
                </ReferenceInput>
            </FormTab>
        </TabbedForm>
    </Edit>
);

export const PodcastEpisodeList = props => (
    <List {...props}>
        <Datagrid>
            <PrivacyField source="privacy" label="ah.core.field.privacy" />
            <TextField
                source="title"
                label="ah.podcast.episode.field.title"
                sortable={false}
            />
            <ReferenceField
                source="channel"
                label="ah.podcast.episode.field.channel"
                reference="podcast_channels"
                sortable={false}
            >
                <TextField source="title" />
            </ReferenceField>
            <DateField source="createdAt" label="ah.core.field.createdAt" />
            <ShowButton />
            <EditButton />
        </Datagrid>
    </List>
);

export const PodcastEpisodeCreate = props => (
    <Create {...props}>
        <TabbedForm>
            <FormTab label="ah.podcast.episode.formtab.setup">
                <ReferenceInput
                    source="channel"
                    reference="podcast_channels"
                    label="ah.podcast.episode.input.channel"
                    allowEmpty
                >
                    <SelectInput optionValue="@id" optionText="title" />
                </ReferenceInput>
                <TextInput
                    source="title"
                    label="ah.podcast.episode.input.title"
                />
                <TextInput
                    source="subtitle"
                    label="ah.podcast.episode.input.subtitle"
                />
                <LongTextInput
                    source="description"
                    label="ah.podcast.episode.input.description"
                />
                <ReferenceInput
                    source="file"
                    reference="files"
                    label="ah.podcast.episode.input.audio_file"
                    allowEmpty
                    filter={{ filetype: 'audio' }}
                >
                    <DialogInput />
                </ReferenceInput>
                <ReferenceInput
                    source="artwork"
                    reference="files"
                    label="ah.podcast.episode.input.artwork"
                    allowEmpty
                    filter={{ filetype: 'image' }}
                >
                    <DialogInput />
                </ReferenceInput>
                <SelectInput
                    source="privacy"
                    label="ah.core.input.privacy"
                    choices={PrivacyInputChoices}
                />
                <DependentInput dependsOn="privacy" value="scheduled">
                    <DateTimeInput source="scheduledAt" />
                </DependentInput>
            </FormTab>
            <FormTab label="ah.podcast.episode.formtab.itunes">
                <TextInput
                    source="author"
                    label="ah.podcast.episode.input.itunes.author"
                />
                <BooleanInput
                    source="itunesBlock"
                    label="ah.podcast.episode.input.itunes.block"
                />
                <BooleanInput
                    source="isExplicit"
                    label="ah.podcast.episode.input.itunes.is_explicit"
                />
                <DateTimeInput
                    source="publishedAt"
                    label="ah.podcast.episode.input.itunes.published_at"
                />
            </FormTab>
        </TabbedForm>
    </Create>
);

export const PodcastEpisodeEdit = props => (
    <Edit {...props}>
        <TabbedForm>
            <FormTab label="ah.podcast.episode.formtab.setup">
                <ReferenceInput
                    source="channel.@id"
                    reference="podcast_channels"
                    label="ah.podcast.episode.input.channel"
                    allowEmpty
                >
                    <SelectInput optionValue="@id" optionText="title" />
                </ReferenceInput>
                <TextInput
                    source="title"
                    label="ah.podcast.episode.input.title"
                />
                <TextInput
                    source="subtitle"
                    label="ah.podcast.episode.input.subtitle"
                />
                <LongTextInput
                    source="description"
                    label="ah.podcast.episode.input.description"
                />
                <ReferenceInput
                    source="file"
                    reference="files"
                    label="ah.podcast.episode.input.audio_file"
                    allowEmpty
                    filter={{ filetype: 'audio' }}
                >
                    <DialogInput />
                </ReferenceInput>
                <ReferenceInput
                    source="artwork"
                    reference="files"
                    label="ah.podcast.episode.input.artwork"
                    allowEmpty
                    filter={{ filetype: 'image' }}
                >
                    <DialogInput />
                </ReferenceInput>
                <SelectInput
                    source="privacy"
                    label="ah.core.input.privacy"
                    choices={PrivacyInputChoices}
                />
                <DependentInput dependsOn="privacy" value="scheduled">
                    <DateTimeInput source="scheduledAt" />
                </DependentInput>
            </FormTab>
            <FormTab label="ah.podcast.episode.formtab.itunes">
                <TextInput
                    source="author"
                    label="ah.podcast.episode.input.itunes.author"
                />
                <BooleanInput
                    source="itunesBlock"
                    label="ah.podcast.episode.input.itunes.block"
                />
                <BooleanInput
                    source="isExplicit"
                    label="ah.podcast.episode.input.itunes.is_explicit"
                />
                <DateTimeInput
                    source="publishedAt"
                    label="ah.podcast.episode.input.itunes.published_at"
                />
            </FormTab>
        </TabbedForm>
    </Edit>
);
