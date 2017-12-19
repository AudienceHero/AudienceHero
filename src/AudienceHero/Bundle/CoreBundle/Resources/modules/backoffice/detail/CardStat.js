import React, { cloneElement } from 'react';
import PropTypes from 'prop-types';
import pure from 'recompose/pure';
import { Card, CardTitle } from 'material-ui/Card';
import { translate } from 'react-admin';
import { Col } from 'react-flexbox-grid';
import get from 'lodash.get';

const CardStat = ({
    xs,
    sm,
    md,
    transform,
    lg,
    value,
    source,
    record,
    icon,
    borderColor,
    iconColor,
    label,
    translate,
}) => {
    const styles = {
        card: { borderLeft: 'solid 4px', borderLeftColor: borderColor },
        icon: {
            float: 'right',
            width: 64,
            height: 64,
            padding: 16,
            color: iconColor,
        },
    };

    var display = null;
    if (typeof value !== 'undefined') {
        display = value;
    } else {
        display = get(record, source);
    }

    return (
        <Col xs={xs} sm={sm} md={md} lg={lg}>
            <Card style={styles.card}>
                {icon && cloneElement(icon, { style: styles.icon })} />
                <CardTitle
                    title={transform(display)}
                    subtitle={translate(label)}
                />
            </Card>
        </Col>
    );
};

CardStat.propTypes = {
    borderColor: PropTypes.string,
    iconColor: PropTypes.string,
    icon: PropTypes.node,
    label: PropTypes.string.isRequired,
    value: PropTypes.any,
    source: PropTypes.string,
    record: PropTypes.object,
    translate: PropTypes.func.isRequired,
    xs: PropTypes.number.isRequired,
    sm: PropTypes.number.isRequired,
    md: PropTypes.number.isRequired,
    lg: PropTypes.number.isRequired,
    transform: PropTypes.func.isRequired,
};

const PureCardStat = pure(translate(CardStat));

PureCardStat.defaultProps = {
    borderColor: 'grey',
    iconColor: 'black',
    xs: 12,
    sm: 12,
    md: 6,
    lg: 6,
    transform: value => value,
};

export default PureCardStat;
