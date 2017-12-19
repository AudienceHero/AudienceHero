import React from 'react';
import Dropzone from 'react-dropzone';
import { fileUpload as fileUploadAction } from './actions';
import PropTypes from 'prop-types';
import { connect } from 'react-redux';
import { Card, CardTitle, CardText } from 'material-ui/Card';
import FileUploadCard from './FileUploadCard';

class FileUploader extends React.Component {
    dropzoneRef = null;

    constructor(props) {
        super(props);

        this.state = {
            dropzoneActive: false,
        };

        this.onDragEnter = this.onDragEnter.bind(this);
        this.onDragLeave = this.onDragLeave.bind(this);
        this.onDrop = this.onDrop.bind(this);
        this.open = this.open.bind(this);
    }

    /**
     * @public
     */
    open() {
        this.dropzoneRef.open();
    }

    onDragEnter() {
        this.setState({ dropzoneActive: true });
    }

    onDragLeave() {
        this.setState({ dropzoneActive: false });
    }

    onDrop(accepted, rejected) {
        this.setState({ accepted, dropzoneActive: false });
        accepted.every(file => {
            this.props.fileUpload(file);
        });
    }

    render() {
        const uploads = this.props.uploads;
        const container = [];
        uploads.forEach((value, key, iter) => {
            container.push(<FileUploadCard key={key} {...value} />);
        });

        return (
            <Dropzone
                onDrop={this.onDrop}
                onDragLeave={this.onDragLeave}
                onDragEnter={this.onDragEnter}
                ref={node => {
                    this.dropzoneRef = node;
                }}
                disableClick
                disablePreview
                style={{}}
            >
                {container}
                {this.props.children}
            </Dropzone>
        );
    }
}

FileUploader.propTypes = {
    fileUpload: PropTypes.func.isRequired,
    uploads: PropTypes.any.isRequired,
};

const mapStateToProps = state => ({ uploads: state.ah_file.get('uploads') });

const mapDispatchToProps = {
    fileUpload: fileUploadAction,
};

export default connect(mapStateToProps, mapDispatchToProps, null, {
    withRef: true,
})(FileUploader);
