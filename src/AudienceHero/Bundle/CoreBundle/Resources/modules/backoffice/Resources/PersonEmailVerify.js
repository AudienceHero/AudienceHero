import React from 'react';
import PropTypes from 'prop-types';
import { translate } from 'react-admin';
import { connect } from 'react-redux';
import compose from 'recompose/compose';
import { verifyPersonEmail as verifyPersonEmailAction } from '../actions';
import { Card, CardTitle } from 'material-ui/Card';

export class PersonEmailVerify extends React.Component {
    componentDidMount() {
        const { id, confirmationToken } = this.props.match.params;
        this.props.verifyPersonEmail(id, confirmationToken);
    }

    render() {
        const { translate } = this.props;
        return (
            <Card>
                <CardTitle
                    title={translate('ah.person_email.verifying')}
                    subtitle={translate('ah.person_email.please_wait')}
                />
            </Card>
        );
    }
}

PersonEmailVerify.propTypes = {
    verifyPersonEmail: PropTypes.func.isRequired,
    translate: PropTypes.func.isRequired,
    match: PropTypes.object.isRequired,
};

const enhance = compose(
    translate,
    connect(null, { verifyPersonEmail: verifyPersonEmailAction })
);

export default enhance(PersonEmailVerify);
