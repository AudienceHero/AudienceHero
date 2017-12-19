import React from 'react';
import PropTypes from 'prop-types';
import { CardMedia as MUICardMedia } from 'material-ui/Card';
import FileTitleField from './FileTitleField';
import IconAudio from 'material-ui-icons/Audiotrack';
import IconVideo from 'material-ui-icons/Movie';
import IconFile from 'material-ui-icons/InsertDriveFile';
import { Image } from '@audiencehero-backoffice/core';
import { CardTitle } from 'material-ui/Card';
import Avatar from 'material-ui/Avatar';
import get from 'lodash/get';

export class CardMediaBody extends React.Component {
    render() {
        const { record } = this.props;
        const iconColor = '#BCBCBC';
        let wrapped = null;
        const fullStyle = {
            width: '100%',
            height: '100%',
            maxWidth: '100%',
            maxHeight: '100%',
        };
        const avatarSize = 96;

        if ('image' === record.filetype) {
            return (
                <Image
                    size="600x0"
                    style={fullStyle}
                    alt={get(record, 'name')}
                    crop="square-center"
                    src={get(record, 'remoteUrl')}
                />
            );
        } else if ('audio' === record.filetype) {
            return <Avatar size={avatarSize} icon={<IconAudio />} />;
        } else if ('video' === record.filetype) {
            return <Avatar size={avatarSize} icon={<IconVideo />} />;
        } else {
            return <Avatar size={avatarSize} icon={<IconFile />} />;
        }
    }
}

CardMediaBody.propTypes = {
    record: PropTypes.object.isRequired,
};

export class CardMedia extends React.Component {
    render() {
        const { record, style } = this.props;
        return (
            <MUICardMedia
                style={style}
                overlay={
                    <CardTitle subtitle={<FileTitleField record={record} />} />
                }
            >
                <CardMediaBody record={record} />
            </MUICardMedia>
        );
    }
}

CardMedia.propTypes = {
    record: PropTypes.object.isRequired,
    style: PropTypes.object,
};
