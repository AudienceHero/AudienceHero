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
import get from 'lodash.get';
import {
    fetchData as fetchDataAction,
    translate,
} from '@audiencehero-frontoffice/core';

export class OptinRequestConfirmed extends React.Component {
    componentDidMount() {
        this.updateData();
    }

    updateData = (id = this.props.id) => {
        this.props.fetchData('contacts_group_forms', id, false);
    };

    render() {
        const { translate, data } = this.props;
        const title = get(data, 'name');
        const description = get(data, 'description');

        return (
            <Card>
                <CardContent>
                    <Typography type="display1" component="h1">
                        {title}
                    </Typography>
                    {description && (
                        <Typography type="subheading" component="p">
                            {description}
                        </Typography>
                    )}
                    <Typography type="subheading" component="p">
                        {translate('ah.contact.optin.explain.optin_confirmed')}
                    </Typography>
                </CardContent>
            </Card>
        );
    }
}

OptinRequestConfirmed.propTypes = {
    id: PropTypes.string.isRequired,
    data: PropTypes.object.isRequired,
    fetchData: PropTypes.func.isRequired,
    translate: PropTypes.func.isRequired,
};

const mapStateToProps = (state, props) => {
    const id = decodeURIComponent(props.match.params.id);

    return {
        id,
        data: state.ah_core.data.get(id) || {},
    };
};

const enhance = compose(
    translate,
    connect(mapStateToProps, {
        fetchData: fetchDataAction,
    })
);

export default enhance(OptinRequestConfirmed);
