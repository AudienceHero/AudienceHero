import React from 'react';
import { Card, CardTitle, CardText } from 'material-ui/Card';
import {LinearProgress} from 'material-ui/Progress';
import PropTypes from 'prop-types';

export default class FileUploadCard extends React.Component {
    render() {
        const progress = this.props.progress * 100;
        let mode = 'determinate';
        if (progress >= 99) {
            mode = 'indeterminate';
        }

        return (
            <Card style={{ marginBottom: '1em' }}>
                <CardTitle>{this.props.file.name}</CardTitle>
                <CardText>
                    <LinearProgress value={progress} mode={mode} />
                </CardText>
            </Card>
        );
    }
}

FileUploadCard.propTypes = {
    file: PropTypes.instanceOf(File),
    progress: PropTypes.number,
};
