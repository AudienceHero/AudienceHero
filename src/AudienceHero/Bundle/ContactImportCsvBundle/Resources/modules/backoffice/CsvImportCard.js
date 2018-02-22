import React from 'react';
import compose from 'recompose/compose';
import { Card, CardContent, CardTitle, Button } from 'material-ui';
import {
    FormField,
    translate,
    ReferenceInput,
    SelectInput,
    Labeled,
    ViewTitle,
    SimpleForm,
    Toolbar,
    SaveButton,
    FileInput,
    FileField,
} from 'react-admin';
import { reduxForm } from 'redux-form';
import ImportIcon from 'material-ui-icons/FileUpload';
import { uploadCsvFile as uploadCsvFileAction } from './actions';
import { connect } from 'react-redux';

export class BaseCsvImportForm extends React.Component {
    render() {
        const { children } = this.props;
        return (
            <form>
                {React.Children.map(
                    children,
                    input =>
                        input && (
                            <div
                                key={input.props.source}
                                className={`aor-input-${input.props.source}`}
                                style={input.props.style}
                            >
                                <FormField
                                    name={input.props.source}
                                    input={input}
                                    resource=""
                                    record={{}}
                                    basePath={''}
                                />
                            </div>
                        )
                )}
            </form>
        );
    }
}

export const CsvImportForm = compose(
    reduxForm({ form: 'csv-import-form', enableReinitialize: true }),
    translate
)(BaseCsvImportForm);

const validateFile = value => {
    if (!Array.isArray(value)) {
        return ['Unknown value'];
    }

    if (value.length === 0) {
        return ['ah.csv_import.validate.file_required'];
    }

    if (value.length > 1) {
        return ['ah.csv_import.validate.please_select_only_one_file'];
    }

    if (value[0].rawFile.type !== 'text/csv') {
        return ['ah.csv_import.validate.csv_file_only'];
    }
};

export class CsvImportCard extends React.Component {
    state = {
        file: null,
        group: null,
    };

    onFileChange = file => {
        if (!file[0]) {
            return;
        }

        this.setState({ file: file[0].rawFile });
    };

    onGroupChange = (event, group) => {
        this.setState({ group });
    };

    save = values => {
        const { file, group } = this.state;
        const reader = new FileReader();
        reader.onload = e => {
            this.props.uploadCsvFile({ text: reader.result, group });
        };
        reader.readAsText(file);
    };

    render() {
        const { translate } = this.props;

        return (
            <Card>
                <CardTitle
                    actAsExpander
                    showExpandableButton
                    title={translate('ah.csv_import.card.title')}
                    subtitle={translate('ah.csv_import.card.subtitle')}
                />
                <CardContent expandable={true}>
                    <CsvImportForm>
                        <FileInput
                            onChange={this.onFileChange}
                            source="file"
                            label="ah.csv_import.input.file"
                            accept="text/csv"
                            validate={validateFile}
                        >
                            <FileField source="src" title="title" />
                        </FileInput>
                        <ReferenceInput
                            onChange={this.onGroupChange}
                            allowEmpty={true}
                            source="group"
                            reference="contacts_groups"
                            label="ah.csv_import.input.group"
                            record={{ id: null }}
                        >
                            <SelectInput
                                optionValue="@id"
                                elStyle={{ width: '100%' }}
                            />
                        </ReferenceInput>
                        <Button raised
                            onClick={this.save}
                            style={{ marginTop: '1em' }}
                            label={translate('ah.csv_import.card.button')}
                            primary={true}
                            icon={<ImportIcon />}
                        />
                    </CsvImportForm>
                </CardContent>
            </Card>
        );
    }
}

export default compose(
    translate,
    connect(null, {
        uploadCsvFile: uploadCsvFileAction,
    })
)(CsvImportCard);
