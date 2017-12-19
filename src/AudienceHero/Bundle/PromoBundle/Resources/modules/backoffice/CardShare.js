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
import IconPreview from 'material-ui-icons/RemoveRedEye';
import compose from 'recompose/compose';
import copy from 'copy-to-clipboard';
import { connect } from 'react-redux';
import { Col } from 'react-flexbox-grid';
import { CardRow } from '@audiencehero-backoffice/core';

export class CardShare extends React.Component {
    handleRssCopy = () => {
        copy(get(this.props.record, 'urls.rss_feed'));
        this.props.showNotification('ah.podcast.rss_feed_copied_to_clipboard');
    };

    render() {
        const {
            translate,
            record = {},
            cardStyle = {},
            cardContainerStyle = {},
        } = this.props;

        return (
            <CardRow>
                <Col xs={12} sm={12} md={12} lg={12}>
                    <Card>
                        <CardTitle title={translate('ah.promo.title.share')} />
                        <CardActions>
                            <Button
                                href={get(record, 'urls.preview')}
                                target="_blank"
                                label={translate('ah.promo.button.preview')}
                                icon={<IconPreview />}
                            />
                        </CardActions>
                    </Card>
                </Col>
            </CardRow>
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
