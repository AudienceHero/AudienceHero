import React from 'react';
import PropTypes from 'prop-types';
import get from 'lodash.get';
import pure from 'recompose/pure';
import {
    Card,
    CardText,
    CardHeader,
    CardTitle,
    CardActions,
} from 'material-ui/Card';
import {
    showNotification as showNotificationAction,
    translate,
} from 'react-admin';
import Button from "material-ui/Button"
import IconRssFeed from 'material-ui-icons/RssFeed';
import IconPreview from 'material-ui-icons/RemoveRedEye';
import compose from 'recompose/compose';
import copy from 'copy-to-clipboard';
import { connect } from 'react-redux';
import { Col, Row } from 'react-flexbox-grid';
import { CardRow } from '@audiencehero-backoffice/core';
import { Paper } from 'material-ui';

export class CardFeedbacks extends React.Component {
    handleRssCopy = () => {
        copy(get(this.props.record, 'urls.rss_feed'));
        this.props.showNotification('ah.podcast.rss_feed_copied_to_clipboard');
    };

    formatFavoriteTrack({ favoriteTrack }) {
        return `${this.props.translate(
            'ah.promo.show.favorite_track'
        )}: ${favoriteTrack.title}`;
    }

    formatFeedback({ feedback, rating }) {
        return `“${feedback}” ${rating}/5`;
    }

    formatContactName({ contact: { name } }) {
        return `– ${name}`;
    }

    render() {
        const { translate, record = {} } = this.props;

        console.log(record);
        const feedbacks = [];
        Object.values(record.feedbacks).map((feedback, index) => {
            feedbacks.push(
                <Row>
                    <Col key={index} xs={12} sm={12} md={12} lg={12}>
                        <Card
                            style={{
                                marginLeft: '1em',
                                marginRight: '1em',
                                marginBottom: '2em',
                            }}
                        >
                            <CardTitle
                                title={this.formatFeedback(feedback)}
                                subtitle={this.formatContactName(feedback)}
                            />
                            <CardHeader
                                subtitle={this.formatFavoriteTrack(feedback)}
                            />
                        </Card>
                    </Col>
                </Row>
            );
        });

        return (
            <CardRow>
                <Col xs={12} sm={12} md={12} lg={12}>
                    <Card style={{ paddingTop: '1em' }}>
                        <CardTitle title="ah.promo.title.feedbacks" />
                        {feedbacks}
                    </Card>
                </Col>
            </CardRow>
        );
    }
}

CardFeedbacks.propTypes = {
    cardStyle: PropTypes.object,
    cardContainerStyle: PropTypes.object,
    record: PropTypes.object,
    translate: PropTypes.func.isRequired,
};

const enhance = compose(
    translate,
    connect(null, { showNotification: showNotificationAction })
);

const EnhancedCardFeedbacks = enhance(CardFeedbacks);

export default EnhancedCardFeedbacks;
