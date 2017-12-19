import React from 'react';
import PropTypes from 'prop-types';
import { EditButton, ShowButton, TextField } from 'react-admin';

export default class FileTitleField extends React.Component {
    render() {
        const record = this.props.record;
        return (
            <div>
                <TextField record={record} source="name" />.<TextField record={record} source="extension" />
            </div>
        );
    }
}

FileTitleField.propTypes = {
    record: PropTypes.object.isRequired,
};
