import React from 'react';
import Button from "material-ui/Button"
import NavigationRefresh from 'material-ui-icons/Refresh';
import UploadIcon from 'material-ui-icons/CloudUpload';
import FileUploader from './FileUploader';
import { CardActions } from 'material-ui/Card';

import {
    SimpleForm,
    CreateButton,
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
    Show,
    SimpleShowLayout,
    ListButton,
    DeleteButton,
    translate,
} from 'react-admin';
import ThumbnailField from './field/ThumbnailField';

const cardActionStyle = {
    zIndex: 2,
    display: 'inline-block',
    float: 'right',
};

const FileActions = translate(
    ({
        resource,
        translate,
        filters,
        displayedFilters,
        filterValues,
        basePath,
        showFilter,
        refresh,
        onUploadClick,
    }) => (
        <CardActions style={cardActionStyle}>
            {filters &&
                React.cloneElement(filters, {
                    resource,
                    showFilter,
                    displayedFilters,
                    filterValues,
                    context: 'button',
                })}
            <Button
                primary
                label={translate('ah.file.button.upload')}
                icon={<UploadIcon />}
                onTouchTap={onUploadClick}
            />
            <Button
                primary
                label={translate('aor.action.refresh')}
                onTouchTap={refresh}
                icon={<NavigationRefresh />}
            />
            {/* Add your custom actions */}
        </CardActions>
    )
);

export class FileList extends React.Component {
    render() {
        const props = this.props;
        return (
            <FileUploader>
                <List
                    perPage={30}
                    {...props}
                    sort={{ createdAt: 'desc' }}
                    actions={
                        <FileActions
                            onUploadClick={() => {
                                this.fileUploaderRef.open();
                            }}
                        />
                    }
                >
                    <Datagrid>
                        <ThumbnailField
                            source="preview"
                            label="ah.file.field.preview"
                        />
                        <TextField
                            source="fullname"
                            label="ah.file.field.fullname"
                        />
                        <DateField
                            source="createdAt"
                            label="ah.core.field.createdAt"
                        />
                        <ShowButton />
                        <EditButton />
                    </Datagrid>
                </List>
            </FileUploader>
        );
    }
}

const FileTitle = ({ record }) => {
    return (
        <span>File {record ? `"${record.name}.${record.extension}"` : ''}</span>
    );
};

const required = value => (value ? undefined : 'Required');

export const FileEdit = props => (
    <Edit title={<FileTitle />} {...props}>
        <SimpleForm>
            <TextInput source="name" validate={required} />
            <TextInput source="description" validate={required} />
        </SimpleForm>
    </Edit>
);

export const FileShowActions = ({ basePath, data, refresh }) => (
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

export const FileShow = props => (
    <Show title={<FileTitle />} actions={<FileShowActions />} {...props}>
        <SimpleShowLayout>
            <TextField source="description" />
        </SimpleShowLayout>
    </Show>
);
