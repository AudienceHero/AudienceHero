import React from 'react';
import PropTypes from 'prop-types';
import get from 'lodash.get';
import pure from 'recompose/pure';

import PrivateIcon from 'material-ui-icons/Lock';
import UnlistedIcon from 'material-ui-icons/Link';
import PublicIcon from 'material-ui-icons/Public';
import ScheduledIcon from 'material-ui-icons/Schedule';

const PRIVACY_PRIVATE = 'private';
const PRIVACY_UNLISTED = 'unlisted';
const PRIVACY_PUBLIC = 'public';
const PRIVACY_SCHEDULED = 'scheduled';

export const PrivacyField = ({ source, record = {}, elStyle }) => {
    const value = get(record, source);

    switch (value) {
        case PRIVACY_PRIVATE:
            return <PrivateIcon style={elStyle} />;
        case PRIVACY_UNLISTED:
            return <UnlistedIcon style={elStyle} />;
        case PRIVACY_PUBLIC:
            return <PublicIcon style={elStyle} />;
        case PRIVACY_SCHEDULED:
            return <ScheduledIcon style={elStyle} />;
        default:
            return <span style={elStyle} />;
    }
};

PrivacyField.propTypes = {
    addLabel: PropTypes.bool,
    elStyle: PropTypes.object,
    label: PropTypes.string,
    record: PropTypes.object,
    source: PropTypes.string.isRequired,
};

const PurePrivacyField = pure(PrivacyField);

PurePrivacyField.defaultProps = {
    addLabel: true,
    elStyle: {
        display: 'block',
        margin: 'auto',
    },
};

export default PurePrivacyField;
