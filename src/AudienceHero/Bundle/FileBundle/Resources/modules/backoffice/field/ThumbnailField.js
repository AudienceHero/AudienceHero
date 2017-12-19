import React from 'react';
import PropTypes from 'prop-types';
import get from 'lodash.get';
import pure from 'recompose/pure';
import { Image } from '@audiencehero-backoffice/core';
import FileIcon from 'material-ui-icons/InsertDriveFile';
import VideoIcon from 'material-ui-icons/Movie';
import AudioIcon from 'material-ui-icons/Audiotrack';

const sizeStyle = { width: '60px', height: '60px' };
const paddingStyle = { padding: '10px' };
const style = { ...paddingStyle, ...sizeStyle };

class ThumbnailField extends React.Component {
    render() {
        const { record, elStyle } = this.props;

        const filetype = get(record, 'filetype');
        if ('image' === filetype) {
            return (
                <Image
                    style={{ ...style, ...elStyle }}
                    src={get(record, 'remoteUrl')}
                    size="60x0"
                    crop="square-center"
                />
            );
        }

        if ('audio' === filetype) {
            return <AudioIcon style={{ ...style, ...elStyle }} />;
        }
        if ('video' === filetype) {
            return <VideoIcon style={{ ...style, ...elStyle }} />;
        }

        return <FileIcon style={{ ...style, ...elStyle }} />;
    }
}

ThumbnailField.propTypes = {
    addLabel: PropTypes.bool,
    elStyle: PropTypes.object,
    label: PropTypes.string,
    record: PropTypes.object,
    source: PropTypes.string.isRequired,
};

const PureThumbnailField = pure(ThumbnailField);

PureThumbnailField.defaultProps = {
    addLabel: true,
};

export default PureThumbnailField;
