import React from 'react';
import PropTypes from 'prop-types';
import get from 'lodash.get';
import pure from 'recompose/pure';

import DraftIcon from 'material-ui-icons/Edit';
import PendingIcon from 'material-ui-icons/Schedule';
import DeliveringIcon from 'material-ui-icons/Send';
import DeliveredIcon from 'material-ui-icons/Send';

const STATUS_DRAFT = 'draft';
const STATUS_PENDING = 'pending';
const STATUS_DELIVERING = 'delivering';
const STATUS_DELIVERED = 'delivered';

export const StatusField = ({ source, record = {}, elStyle }) => {
    const value = get(record, source);

    switch (value) {
        case STATUS_DRAFT:
            return <DraftIcon style={elStyle} />;
        case STATUS_PENDING:
            return <PendingIcon style={elStyle} />;
        case STATUS_DELIVERING:
            return <DeliveringIcon style={elStyle} />;
        case STATUS_DELIVERED:
            return <DeliveredIcon style={elStyle} />;
        default:
            return <span style={elStyle} />;
    }
};

StatusField.propTypes = {
    addLabel: PropTypes.bool,
    elStyle: PropTypes.object,
    label: PropTypes.string,
    record: PropTypes.object,
    source: PropTypes.string.isRequired,
};

const PureStatusField = pure(StatusField);

PureStatusField.defaultProps = {
    addLabel: true,
    elStyle: {
        display: 'block',
        margin: 'auto',
    },
};

export default PureStatusField;
