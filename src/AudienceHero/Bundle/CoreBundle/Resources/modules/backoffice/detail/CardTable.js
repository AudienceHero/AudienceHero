import React, { cloneElement } from 'react';
import PropTypes from 'prop-types';
import pure from 'recompose/pure';
import { Card, CardTitle, CardText } from 'material-ui/Card';
import { translate } from 'react-admin';
import { Col } from 'react-flexbox-grid';

const CardTable = ({ xs, sm, md, lg, label, children, translate }) => {
    return (
        <Col xs={xs} sm={sm} md={md} lg={lg}>
            <Card>
                <CardTitle title={translate(label)} />
                <CardText>
                    {React.Children.map(children, child => {
                        return React.cloneElement(child);
                    })}
                </CardText>
            </Card>
        </Col>
    );
};

CardTable.propTypes = {
    children: PropTypes.node.isRequired,
    label: PropTypes.string.isRequired,
    translate: PropTypes.func.isRequired,
    xs: PropTypes.number.isRequired,
    sm: PropTypes.number.isRequired,
    md: PropTypes.number.isRequired,
    lg: PropTypes.number.isRequired,
};

const PureCardTable = pure(translate(CardTable));

PureCardTable.defaultProps = {
    xs: 12,
    sm: 12,
    md: 12,
    lg: 12,
};

export default PureCardTable;
