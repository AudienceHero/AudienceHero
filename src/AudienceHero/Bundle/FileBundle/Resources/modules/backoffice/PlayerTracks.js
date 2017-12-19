import React from 'react';
import Button from "material-ui/Button"
import TextField from 'material-ui/TextField';
import IconArrowDownward from 'material-ui-icons/ArrowDownward';
import IconArrowUpward from 'material-ui-icons/ArrowUpward';
import IconDelete from 'material-ui-icons/Delete';
import {
    Field,
    FieldArray,
    change as fieldChangeAction,
    arrayRemove as arrayRemoveAction,
    getFormValues,
} from 'redux-form';
import { ReferenceInput, TextInput } from 'react-admin';
import {
    Table,
    TableBody,
    TableHeader,
    TableHeaderColumn,
    TableRow,
    TableRowColumn,
} from 'material-ui/Table';
import DialogInput from './DialogInput';
import { connect } from 'react-redux';
import isPlainObject from 'lodash.isplainobject';
import get from 'lodash.get';

const renderTextField = ({
    input,
    label,
    meta: { touched, error },
    ...custom
}) => {
    return (
        <TextField
            hintText={label}
            floatingLabelText={label}
            errorText={touched && error}
            onChange={input.onChange}
            value={input.value}
            {...input}
            {...custom}
        />
    );
};

export class BasePlayerTracks extends React.Component {
    state = {
        tracks: [],
    };

    moveDown = index => {
        this.changePosition(index, index + 1);
        this.props.fields.move(index, index + 1);
        this.changePosition(index, index);
    };

    changePosition = (index, position) => {
        const formName = this.props.meta.form;
        const field = `tracks[${index}].position`;
        this.props.change(formName, field, position);
    };

    moveUp = index => {
        this.changePosition(index, index - 1);
        this.props.fields.move(index, index - 1);
        this.changePosition(index, index);
    };

    remove = indexToRemove => {
        const lastIndex = this.props.fields.length;

        for (let i = indexToRemove; i < lastIndex; i++) {
            this.moveDown(i);
        }
        this.props.fields.remove(lastIndex);
        this.props.arrayRemove(this.props.meta.form, 'tracks', lastIndex - 1);
    };

    updateFromProps = props => {
        const tracks = props.values.tracks.map((track, index) => {
            if (isPlainObject(track.file)) {
                return track.file;
            }

            return track.audio;
        });
        console.log(tracks);
        this.setState({ tracks });
    };

    componentWillReceiveProps(props) {
        this.updateFromProps(props);
    }

    componentWillMount() {
        this.updateFromProps(this.props);
    }

    render() {
        const { fields, meta: { error, submitFailed } } = this.props;

        return (
            <div>
                <Table selectable={false}>
                    <TableBody displayRowCheckbox={false}>
                        {fields.map((track, index) => {
                            const remoteUrl = get(
                                this.state,
                                `tracks[${index}].remoteUrl`
                            );
                            console.log(this.state, index, remoteUrl);
                            return (
                                <TableRow key={index}>
                                    <TableRowColumn>
                                        {index + 1}
                                        <Field
                                            name={`${track}.position`}
                                            component="input"
                                            type="hidden"
                                            value={parseInt(index)}
                                        />
                                    </TableRowColumn>
                                    <TableRowColumn>
                                        <Field
                                            name={`${track}.title`}
                                            component={renderTextField}
                                            label="Title"
                                        />
                                    </TableRowColumn>
                                    <TableRowColumn>
                                        <Field
                                            name={`${track}.file`}
                                            component="input"
                                            type="hidden"
                                        />
                                        <audio
                                            style={{ marginTop: '1em' }}
                                            src={remoteUrl}
                                            controls
                                        />
                                    </TableRowColumn>
                                    <TableRowColumn>
                                        <Button
                                            disabled={
                                                fields.length === index + 1
                                            }
                                            onClick={() => this.moveDown(index)}
                                            icon={<IconArrowDownward />}
                                        />
                                        <Button
                                            disabled={0 === index}
                                            onClick={() => this.moveUp(index)}
                                            icon={<IconArrowUpward />}
                                        />
                                        <Button
                                            onClick={() => this.remove(index)}
                                            icon={<IconDelete />}
                                        />
                                    </TableRowColumn>
                                </TableRow>
                            );
                        })}
                    </TableBody>
                </Table>
            </div>
        );
    }
}

export const PlayerTracks = connect(
    state => ({
        values: getFormValues('record-form')(state),
    }),
    {
        change: fieldChangeAction,
        arrayRemove: arrayRemoveAction,
    }
)(BasePlayerTracks);
