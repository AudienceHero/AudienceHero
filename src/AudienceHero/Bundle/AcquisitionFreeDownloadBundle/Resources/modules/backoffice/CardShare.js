import React from 'react';
import PropTypes from 'prop-types';
import get from 'lodash.get';
import { Card, CardTitle, CardActions } from 'material-ui/Card';
import Button from "material-ui/Button"
import IconWorld from 'material-ui-icons/Public';
import compose from 'recompose/compose';
import { translate } from 'react-admin';
import { Col, Row } from 'react-flexbox-grid';

export class CardShare extends React.Component {
    render() {
        const {
            translate,
            record = {},
            cardStyle = {},
            cardContainerStyle = {},
        } = this.props;

        return (
            <Row style={{ marginBottom: '1em' }}>
                <Col xs={12} sm={12} md={12} lg={12}>
                    <Card
                        containerStyle={cardContainerStyle}
                        style={{ width: '100%', ...cardStyle }}
                    >
                        <CardTitle title={translate('ah.afd.title.share')} />
                        <CardActions>
                            <Button
                                href={get(record, 'urls.preview')}
                                target="_blank"
                                label={translate('ah.afd.action.preview')}
                                icon={<IconWorld />}
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

const enhance = compose(translate);

const EnhancedCardShare = enhance(CardShare);

export default EnhancedCardShare;
