import React from 'react';
import {
    List,
    SimpleForm,
    TextInput,
    ReferenceInput,
    Create,
    Edit,
    Datagrid,
    TextField,
    DateField,
    ShowButton,
    EditButton,
    ReferenceArrayInput,
    LongTextInput,
} from 'react-admin';
import { PlayerTracks } from './PlayerTracks';
import { FieldArray } from 'redux-form';
import DialogInput from './DialogInput';
import compose from 'recompose/compose';
import { connect } from 'react-redux';
import { arrayPush as arrayPushAction, formValueSelector } from 'redux-form';

export const PlayerList = props => (
    <List {...props} sort={{ field: 'createdAt', order: 'DESC' }}>
        <Datagrid>
            <TextField source="reference" label="ah.core.field.reference" />
            <TextField
                source="title"
                label="ah.file.player.field.title"
                sortable={false}
            />
            <DateField source="createdAt" label="ah.core.field.createdAt" />
            <ShowButton />
            <EditButton />
        </Datagrid>
    </List>
);

const formSelector = formValueSelector('record-form');
const connectFields = connect(
    state => {
        return {
            tracks: formSelector(state, 'tracks') || [],
        };
    },
    { arrayPush: arrayPushAction }
);

export class BasePlayerCreate extends React.Component {
    onFilePick = value => {
        this.props.arrayPush('record-form', 'tracks', {
            title: value.name,
            position: this.props.tracks.length,
            file: value['@id'],
            audio: value,
        });
    };

    render() {
        const props = this.props;
        return (
            <div>
                <Create {...props}>
                    <SimpleForm>
                        <LongTextInput
                            source="reference"
                            label="ah.core.input.reference"
                        />
                        <TextInput
                            source="title"
                            label="ah.file.player.input.title"
                        />
                        <FieldArray
                            name="tracks"
                            label="ah.file.player.input.tracks"
                            component={PlayerTracks}
                            rerenderOnEveryChange
                        />
                        <ReferenceInput
                            filter={{ filetype: 'audio' }}
                            allowEmpty={true}
                            resource="files"
                            reference="files"
                            label="ah.file.player.input.pick_track"
                        >
                            <DialogInput
                                onFilePick={this.onFilePick}
                                displayChoice={false}
                            />
                        </ReferenceInput>
                    </SimpleForm>
                </Create>
            </div>
        );
    }
}

const enhance = compose(connectFields);

export const PlayerCreate = enhance(BasePlayerCreate);

export class BasePlayerEdit extends React.Component {
    onFilePick = value => {
        this.props.arrayPush('record-form', 'tracks', {
            title: value.name,
            position: this.props.tracks.length,
            file: value['@id'],
            audio: value,
        });
    };

    render() {
        const props = this.props;
        return (
            <div>
                <Edit {...props}>
                    <SimpleForm>
                        <LongTextInput
                            source="reference"
                            label="ah.core.input.reference"
                        />
                        <TextInput
                            source="title"
                            label="ah.file.player.input.title"
                        />
                        <FieldArray
                            name="tracks"
                            label="ah.file.player.input.tracks"
                            component={PlayerTracks}
                            rerenderOnEveryChange
                        />
                        <ReferenceInput
                            filter={{ filetype: 'audio' }}
                            allowEmpty={true}
                            resource="files"
                            reference="files"
                            label="ah.file.player.input.pick_track"
                        >
                            <DialogInput
                                onFilePick={this.onFilePick}
                                displayChoice={false}
                            />
                        </ReferenceInput>
                    </SimpleForm>
                </Edit>
            </div>
        );
    }
}

const enhanceEdit = compose(connectFields);

export const PlayerEdit = enhanceEdit(BasePlayerEdit);
