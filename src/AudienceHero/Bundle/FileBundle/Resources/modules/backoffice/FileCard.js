import React from 'react';
import PropTypes from 'prop-types';
import { translate } from 'react-admin';
import Button from "material-ui/Button"
import { Card, CardTitle, CardHeader, CardActions } from 'material-ui/Card';
import { Image } from '@audiencehero-backoffice/core';
import { CardMedia } from './CardMedia';

const cardStyle = {
    flexBasis: '15em',
    margin: '0.5em',
    display: 'inline-block',
    verticalAlign: 'top',
};

class FileCard extends React.Component {
    render() {
        const { record, translate, handleClick } = this.props;

        const clickHandler = () => {
            handleClick(record);
        };

        return (
            <Card style={{ ...cardStyle, ...this.props.cardStyle }}>
                <CardMedia
                    record={record}
                    style={{
                        width: '15em',
                        height: '15em',
                        overflow: 'hidden',
                        textAlign: 'center',
                        display: 'flex',
                        justifyContent: 'center',
                        alignItems: 'center',
                    }}
                />
                <CardActions>
                    <Button
                        label={translate(this.props.buttonLabel)}
                        onTouchTap={clickHandler}
                        primary={true}
                        fullWidth={true}
                    />
                </CardActions>
            </Card>
        );
    }
}

FileCard.defaultProps = {
    cardStyle: {},
    showActions: true,
};

FileCard.propTypes = {
    record: PropTypes.object.isRequired,
    basePath: PropTypes.string.isRequired,
    cardStyle: PropTypes.object,
    showActions: PropTypes.bool,
    handleClick: PropTypes.func,
    translate: PropTypes.func.isRequired,
    buttonLabel: PropTypes.string.isRequired,
};

FileCard.defaultProps = {
    buttonLabel: 'ah.file.dialog.picker.button.pick',
};

export default translate(FileCard);
