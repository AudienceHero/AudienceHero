import React, { Component, Children } from 'react';
import PropTypes from 'prop-types';
import { connect } from 'react-redux';
import { Card, CardActions } from 'material-ui/Card';
import compose from 'recompose/compose';
import inflection from 'inflection';
import {
    translate,
    EditButton,
    ListButton,
    DeleteButton,
    RefreshButton,
    crudGetOne as crudGetOneAction,
    Title,
    ViewTitle,
} from 'react-admin';
import { Col, Grid, Row } from 'react-flexbox-grid';

const cardActionStyle = {
    zIndex: 2,
    display: 'inline-block',
    float: 'right',
};

const ShowActions = ({ basePath, data, hasDelete, hasEdit, refresh }) => (
    <CardActions style={cardActionStyle}>
        {hasEdit && <EditButton basePath={basePath} record={data} />}
        <ListButton basePath={basePath} />
        {hasDelete && <DeleteButton basePath={basePath} record={data} />}
        <RefreshButton refresh={refresh} />
    </CardActions>
);

export class CardShow extends Component {
    componentDidMount() {
        this.updateData();
    }

    componentWillReceiveProps(nextProps) {
        if (this.props.id !== nextProps.id) {
            this.updateData(nextProps.resource, nextProps.id);
        }
    }

    getBasePath() {
        const { location } = this.props;
        return location.pathname
            .split('/')
            .slice(0, -2)
            .join('/');
    }

    updateData(resource = this.props.resource, id = this.props.id) {
        this.props.crudGetOne(resource, id, this.getBasePath());
    }

    refresh = event => {
        event.stopPropagation();
        this.fullRefresh = true;
        this.updateData();
    };

    render() {
        const {
            actions = <ShowActions />,
            title,
            children,
            id,
            data,
            isLoading,
            resource,
            hasDelete,
            hasEdit,
            translate,
        } = this.props;
        const basePath = this.getBasePath();

        const resourceName = translate(`resources.${resource}.name`, {
            smart_count: 1,
            _: inflection.humanize(inflection.singularize(resource)),
        });
        const defaultTitle = translate('aor.page.show', {
            name: `${resourceName}`,
            id,
            data,
        });
        const titleElement = data ? (
            <Title title={title} record={data} defaultTitle={defaultTitle} />
        ) : (
            ''
        );

        return (
            <Grid fluid>
                <Row style={{ marginBottom: '1em' }}>
                    <Col xs={12} sm={12} lg={12} md={12}>
                        <Card style={{ opacity: isLoading ? 0.8 : 1 }}>
                            {actions &&
                                React.cloneElement(actions, {
                                    basePath,
                                    data,
                                    hasDelete,
                                    hasEdit,
                                    refresh: this.refresh,
                                    resource,
                                })}
                            <ViewTitle title={titleElement} />
                        </Card>
                    </Col>
                </Row>
                {data &&
                    Children.map(children, child => {
                        return React.cloneElement(child, {
                            resource,
                            basePath,
                            record: data,
                            translate,
                        });
                    })}
            </Grid>
        );
    }
}

CardShow.propTypes = {
    actions: PropTypes.element,
    children: PropTypes.oneOfType([PropTypes.element, PropTypes.array]),
    crudGetOne: PropTypes.func.isRequired,
    data: PropTypes.object,
    hasDelete: PropTypes.bool,
    hasEdit: PropTypes.bool,
    id: PropTypes.string.isRequired,
    isLoading: PropTypes.bool.isRequired,
    location: PropTypes.object.isRequired,
    match: PropTypes.object.isRequired,
    resource: PropTypes.string.isRequired,
    title: PropTypes.any,
    translate: PropTypes.func,
};

function mapStateToProps(state, props) {
    return {
        id: decodeURIComponent(props.match.params.id),
        data:
            state.admin.resources[props.resource].data[
                decodeURIComponent(props.match.params.id)
            ],
        isLoading: state.admin.loading > 0,
    };
}

const enhance = compose(
    connect(mapStateToProps, { crudGetOne: crudGetOneAction }),
    translate
);

export default enhance(CardShow);
