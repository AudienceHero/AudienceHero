import React from 'react';
import PropTypes from 'prop-types';
import compose from 'recompose/compose';
import { connect } from 'react-redux';
import {
    SelectInput,
    translate,
    crudGetOne as crudGetOneAction,
} from 'react-admin';
import {
    Button,
    Card,
    CardContent,
    Table,
    TableBody,
    TableHead,
    TableRow,
    Select,
    MenuItem,
} from 'material-ui';
import { importCsvContacts as importCsvContactsAction } from './actions';
import get from 'lodash.get';
import { Field, reduxForm } from 'redux-form';

const renderSelect = (muiTheme, choices, translate) => props => {
    const { input, meta: { touched, error } } = props;

    return (
        <Select
            onChange={(e, k, v) => {
                input.onChange(v);
            }}
            value={input.value}
            style={{ maxWidth: '100%' }}
            floatingLabelText={translate('ah.csv_import.input.select_column')}
            errorText={error && translate(error)}
        >
            {choices.map(item => (
                <MenuItem
                    key={item}
                    value={item}
                    primaryText={translate(`ah.csv_import.column.${item}`)}
                />
            ))}
        </Select>
    );
};

export class BaseForm extends React.Component {
    cellWidth = '200px';

    renderTableRows = () => {
        const { metadata: { header, sample }, muiTheme } = this.props;
        const borderColor = muiTheme.tableRow.borderColor;

        const style = {
            minWidth: this.cellWidth,
            width: this.cellWidth,
            overflowX: 'none',
            whiteSpace: 'none',
            border: `1px solid ${borderColor}`,
            borderTop: 'none',
        };

        const rows = [];
        rows.push(
            <TableRow
                key="header"
                displayBorder
                style={{ backgroundColor: muiTheme.tableRow.hoverColor }}
            >
                {header.map(key => {
                    return (
                        <TableCell style={style} key={key}>
                            {key}
                        </TableCell>
                    );
                })}
            </TableRow>
        );

        var index = 0;
        for (const extract of sample) {
            const columns = [];
            for (const key of header) {
                columns.push(
                    <TableCell style={style} key={key}>
                        {extract[key]}
                    </TableCell>
                );
            }
            rows.push(
                <TableRow key={index} displayBorder>
                    {columns}
                </TableRow>
            );
            index++;
        }

        return rows;
    };

    renderTableHeader = () => {
        const {
            metadata: { contact_matches, contact_choices },
            muiTheme,
            translate,
        } = this.props;
        const borderColor = muiTheme.tableRow.borderColor;
        const style = { border: `1px solid ${borderColor}`, height: 'auto' };

        const columns = [];
        Object.keys(contact_matches).forEach(key => {
            columns.push(
                <TableCell key={key} style={style}>
                    <Field
                        name={key}
                        type="text"
                        component={renderSelect(
                            muiTheme,
                            contact_choices,
                            translate
                        )}
                    />
                </TableCell>
            );
        });

        return columns;
    };

    render() {
        const { handleSubmit, translate } = this.props;
        return (
            <form>
                <Table
                    selectable={false}
                    style={{
                        borderSpacing: '1em 0',
                        borderCollapse: 'separate',
                    }}
                >
                    <TableHead displaySelectAll={false}>
                        {this.renderTableHeader()}
                    </TableHead>
                    <TableBody displayRowCheckbox={false}>
                        {this.renderTableRows()}
                    </TableBody>
                </Table>
                <Button raised
                    style={{ marginTop: '2em' }}
                    onClick={handleSubmit}
                    primary
                    label={translate('ah.csv_import.match.submit')}
                />
            </form>
        );
    }
}

BaseForm.propTypes = {
    header: PropTypes.array.isRequired,
    choices: PropTypes.array.isRequired,
    matches: PropTypes.object.isRequired,
    sample: PropTypes.array.isRequired,
};

const Form = compose(
    translate,
    reduxForm({
        form: 'csv-import-form',
        validate: (values, props) => {
            const { metadata: { header } } = props;
            const errors = {};
            header.forEach(key => {
                if (!values[key]) {
                    errors[key] = 'ah.csv_import.validate.match_required';
                }
            });

            var valuesOnly = Object.values(values);
            const duplicates = valuesOnly.reduce(function(acc, el, i, arr) {
                if (arr.indexOf(el) !== i && acc.indexOf(el) < 0) acc.push(el);
                return acc;
            }, []);

            Object.keys(valuesOnly).forEach(key => {
                const value = valuesOnly[key];
                if (duplicates.indexOf(value) > -1) {
                    errors[key] = 'ah.csv_import.validate.duplicate_choice';
                }
            });

            if (
                valuesOnly.indexOf('full_name') > -1 &&
                (valuesOnly.indexOf('first_name') > -1 ||
                    valuesOnly.indexOf('last_name') > -1)
            ) {
                Object.keys(values).forEach(key => {
                    if (
                        values[key] === 'full_name' ||
                        values[key] === 'first_name' ||
                        values[key] === 'last_name'
                    ) {
                        errors[key] =
                            'ah.csv_import.validate.first_last_or_full_name';
                    }
                });
            }

            return errors;
        },
    }),
)(BaseForm);

export class CsvContactImport extends React.Component {
    componentDidMount() {
        this.updateData();
    }

    updateData = (id = this.props.id) => {
        this.props.crudGetOne('text_stores', id, '');
    };

    submit = values => {
        const { id, data: { metadata } } = this.props;
        this.props.importCsvContacts({
            id,
            metadata: { ...metadata, contact_matches: values },
        });
    };

    render() {
        const { data, translate } = this.props;

        if (typeof data === 'undefined') {
            return false;
        }

        const { metadata } = data;

        return (
            <Card>
                <CardContent>
                    <Typography gutterBottom variant="headline" component="h2">{translate('ah.csv_import.match.title')}</Typography>
                    <Typography component="p">{translate('ah.csv_import.match.subtitle')}</Typography>
                </CardContent>
                <CardContent>
                    <Form
                        onSubmit={this.submit}
                        metadata={metadata}
                        initialValues={metadata.contact_matches}
                    />
                </CardContent>
            </Card>
        );
    }
}

CsvContactImport.propTypes = {
    id: PropTypes.string.isRequired,
    translate: PropTypes.func.isRequired,
    crudGetOne: PropTypes.func.isRequired,
    importCsvContacts: PropTypes.func.isRequired,
};

const mapStateToProps = (state, props) => {
    const id = decodeURIComponent(props.match.params.id);
    const data = get(state, `admin.resources.text_stores.data.${id}`);

    return { id, data };
};

const enhance = compose(
    translate,
    connect(mapStateToProps, {
        crudGetOne: crudGetOneAction,
        importCsvContacts: importCsvContactsAction,
    }),
);

const EnhancedCsvContactImport = enhance(CsvContactImport);

export default EnhancedCsvContactImport;
