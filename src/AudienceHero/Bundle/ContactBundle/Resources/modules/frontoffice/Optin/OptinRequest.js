import PropTypes from 'prop-types';
import { connect } from 'react-redux';
import React from 'react';
import compose from 'recompose/compose';
import Card, {
    CardActions,
    CardContent,
    CardMedia,
} from 'material-ui/Card';
import Typography from 'material-ui/Typography';
import { optin as optinAction } from '../actions';
import get from 'lodash.get';
import OptinRequestForm from './OptinRequestForm';
import {
    fetchData as fetchDataAction,
    translate,
} from '@audiencehero-frontoffice/core';

export class OptinRequest extends React.Component {
    componentDidMount() {
        this.updateData();
    }

    updateData = (id = this.props.id) => {
        this.props.fetchData('contacts_group_forms', id, false);
    };

    handleSubmit = values => {
        this.props.optin({ id: this.props.id, values });
    };

    render() {
        const { data } = this.props;
        const title = get(data, 'name');
        const description = get(data, 'description');

        return (
            <Card>
                <CardContent>
                    <Typography type="display1" component="h1">
                        {title}
                    </Typography>
                    {description && (
                        <Typography type="body1" component="p">
                            {description}
                        </Typography>
                    )}
                    <OptinRequestForm
                        onSubmit={this.handleSubmit}
                        data={data}
                    />
                </CardContent>
            </Card>
        );
    }
}

OptinRequest.propTypes = {
    id: PropTypes.string.isRequired,
    data: PropTypes.object.isRequired,
    fetchData: PropTypes.func.isRequired,
    optin: PropTypes.func.isRequired,
};

const mapStateToProps = (state, props) => {
    const id = decodeURIComponent(props.match.params.id);

    return {
        id,
        data: state.ah_core.data.get(id) || {},
        submitting: state.ah_contact.submitting,
    };
};

const enhance = compose(
    translate,
    connect(mapStateToProps, {
        fetchData: fetchDataAction,
        optin: optinAction,
    })
);

export default enhance(OptinRequest);
