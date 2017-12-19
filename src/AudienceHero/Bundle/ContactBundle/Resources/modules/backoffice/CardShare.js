import React from 'react';
import PropTypes from 'prop-types';
import get from 'lodash.get';
import { Card, CardTitle, CardActions } from 'material-ui/Card';
import {
    showNotification as showNotificationAction,
    translate,
} from 'react-admin';
import Button from "material-ui/Button"
import IconCopy from 'material-ui-icons/ContentCopy';
import IconWorld from 'material-ui-icons/Public';
import IconPrint from 'material-ui-icons/Print';
import compose from 'recompose/compose';
import copy from 'copy-to-clipboard';
import { connect } from 'react-redux';

export class CardShare extends React.Component {
    handleCopy = () => {
        copy(get(this.props.record, 'urls.public'));
        this.props.showNotification('ah.contact.form_url_copied_to_clipboard');
    };

    render() {
        const {
            translate,
            record = {},
            cardStyle = {},
            cardContainerStyle = {},
        } = this.props;

        return (
            <Card
                containerStyle={cardContainerStyle}
                style={{ width: '100%', ...cardStyle }}
            >
                <CardTitle title={translate('ah.contact.share')} />
                <CardActions>
                    <Button
                        href={get(record, 'urls.public')}
                        target="_blank"
                        label={this.props.translate(
                            'ah.contact.form_public_page'
                        )}
                        icon={<IconWorld />}
                    />
                    <Button
                        target="_blank"
                        label={this.props.translate(
                            'ah.contact.copy_to_clipboard'
                        )}
                        onTouchTap={this.handleCopy}
                        icon={<IconCopy />}
                    />
                    <Button
                        target="_blank"
                        href={get(record, 'urls.print')}
                        label={this.props.translate('ah.contact.print')}
                        icon={<IconPrint />}
                    />
                </CardActions>
            </Card>
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
