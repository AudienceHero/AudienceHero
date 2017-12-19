import React from 'react';
import PropTypes from 'prop-types';
import get from 'lodash.get';
import pure from 'recompose/pure';
import { Card, CardTitle, CardActions } from 'material-ui/Card';
import {
    showNotification as showNotificationAction,
    translate,
} from 'react-admin';
import Button from "material-ui/Button"
import IconRssFeed from 'material-ui-icons/RssFeed';
import IconWorld from 'material-ui-icons/Public';
import compose from 'recompose/compose';
import copy from 'copy-to-clipboard';
import { connect } from 'react-redux';
import { Col, Row } from 'react-flexbox-grid';

export class CardShare extends React.Component {
    handleRssCopy = () => {
        copy(get(this.props.record, 'urls.rss_feed'));
        this.props.showNotification(
            'ah.podcast.notification.rss_feed_copied_to_clipboard'
        );
    };

    render() {
        const { translate, record = {} } = this.props;

        return (
            <Row style={{ marginBottom: '1em' }}>
                <Col xs={12} sm={12} md={12} lg={12}>
                    <Card>
                        <CardTitle
                            title={translate('ah.podcast.title.share')}
                        />
                        <CardActions>
                            <Button
                                href={get(record, 'urls.public')}
                                target="_blank"
                                label={translate('ah.podcast.link.public_page')}
                                icon={<IconWorld />}
                            />
                            <Button raised
                                label={translate(
                                    'ah.podcast.link.copy_rss_feed'
                                )}
                                icon={<IconRssFeed />}
                                onTouchTap={this.handleRssCopy}
                            />
                        </CardActions>
                    </Card>
                </Col>
            </Row>
        );
    }
}

CardShare.propTypes = {
    cardStyle: PropTypes.object,
    cardContainerStyle: PropTypes.object,
    record: PropTypes.object,
    translate: PropTypes.func.isRequired,
};

const enhance = compose(
    translate,
    connect(null, { showNotification: showNotificationAction })
);

const EnhancedCardShare = enhance(CardShare);

export default EnhancedCardShare;
