import React from 'react';
import PropTypes from 'prop-types';
import get from 'lodash.get';
import pure from 'recompose/pure';
import { Card, CardTitle, CardText } from 'material-ui/Card';
import {LinearProgress} from 'material-ui/Progress';
import { Col } from 'react-flexbox-grid';
import { translate } from 'react-admin';

const CardProgress = ({
    xs,
    sm,
    md,
    lg,
    transform,
    value,
    source,
    record = {},
    label,
    translate,
}) => {
    var display = null;
    if (typeof value !== 'undefined') {
        display = value;
    } else {
        display = get(record, source);
    }
    display = transform(display);

    return (
        <Col xs={xs} sm={sm} md={md} lg={lg}>
            <Card>
                <CardText>
                    <LinearProgress
                        mode="determinate"
                        max={100}
                        min={0}
                        value={display}
                    />
                </CardText>
                <CardTitle
                    subtitle={translate(label)}
                    title={`${Number(display).toFixed(1)}%`}
                />
            </Card>
        </Col>
    );
};

CardProgress.propTypes = {
    cardStyle: PropTypes.object,
    cardContainerStyle: PropTypes.object,
    label: PropTypes.string.isRequired,
    record: PropTypes.object,
    source: PropTypes.string.isRequired,
    transform: PropTypes.func.isRequired,
    value: PropTypes.any,
    xs: PropTypes.number.isRequired,
    sm: PropTypes.number.isRequired,
    md: PropTypes.number.isRequired,
    lg: PropTypes.number.isRequired,
};

const PureCardProgress = pure(translate(CardProgress));

CardProgress.defaultProps = {
    transform: value => value,
};

export default PureCardProgress;
