import React from 'react';
import PropTypes from 'prop-types';
import get from 'lodash.get';
import pure from 'recompose/pure';
import { Card, CardTitle } from 'material-ui/Card';

const CardField = ({
    source,
    record = {},
    label,
    cardStyle = {},
    cardContainerStyle = {},
}) => {
    return (
        <Box px={1} py={1}>
            <Card
                containerStyle={cardContainerStyle}
                style={{ width: '100%', ...cardStyle }}
            >
                <CardTitle title={get(record, source)} subtitle={label} />
            </Card>
        </Box>
    );
};

CardField.propTypes = {
    cardStyle: PropTypes.object,
    cardContainerStyle: PropTypes.object,
    label: PropTypes.string.isRequired,
    record: PropTypes.object,
    source: PropTypes.string.isRequired,
};

const PureCardField = pure(CardField);

export default PureCardField;
