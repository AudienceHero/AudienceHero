import PropTypes from 'prop-types';
import React from 'react';
import { connect } from 'react-redux';
import compose from 'recompose/compose';
import { Player } from '@audiencehero-frontoffice/file';
import {
    translate,
    setTitle as setTitleAction,
    fetchData as fetchDataAction,
} from '@audiencehero-frontoffice/core';
import { Button, Typography } from 'material-ui';
import Dialog, {
    DialogActions,
    DialogContent,
    DialogContentText,
    DialogTitle,
} from 'material-ui/Dialog';
import get from 'lodash.get';
import { submitPromoFeedback as submitPromoFeedbackAction } from './actions';
import PromoFeedbackForm from './PromoFeedbackForm';
import { Helmet } from 'react-helmet';
import { recordActivity as recordActivityAction } from '@audiencehero-frontoffice/activity';

export class Promo extends React.Component {
    state = {
        open: false,
        ready: false,
        isPreview: false,
    };

    componentDidMount() {
        // Fetch api resource
        this.updateData();
    }

    componentWillReceiveProps(nextProps) {
        const playerID = get(nextProps.data, '@id');
        if (typeof playerID != 'undefined') {
            this.setState({ ready: true });
            this.props.setTitle(get(nextProps.data, 'player.title'));
        }

        if (this.props.id !== nextProps.id) {
            this.updateData(nextProps.id, nextProps.recipientId);
        }
    }

    updateData(id = this.props.id, recipientId = this.props.recipientId) {
        this.props.fetchData('promos', id, 'preview' === recipientId);
    }

    handleButtonClick = () => {
        this.setState({ open: true });
    };

    handleCloseDialog = () => {
        this.setState({ open: false });
    };

    handleFeedbackSubmission = values => {
        this.props.submitPromoFeedback({
            values,
            promoId: this.props.data['@id'],
            id: this.props.id,
            recipientId: this.props.recipientId,
        });
    };

    render() {
        const { translate, data, downloadUrl, submittingFeedback } = this.props;
        const tracks = data.player ? data.player.tracks : [];

        return (
            <div>
                <Player
                    ready={this.state.ready}
                    artwork={data.artwork || {}}
                    description={data.description}
                    player={data.player || {}}
                    enableShare={false}
                    autoplay
                    onButtonClick={this.handleButtonClick}
                />
                <Dialog
                    open={this.state.open}
                    onRequestClose={this.handleCloseDialog}
                >
                    {null === downloadUrl ? (
                        <PromoFeedbackForm
                            isSubmitting={submittingFeedback}
                            onSubmit={this.handleFeedbackSubmission}
                            tracks={tracks}
                        />
                    ) : (
                        <div>
                            <DialogTitle>
                                {translate('ah.promos.dialog.download')}
                            </DialogTitle>
                            <DialogContent>
                                <Typography
                                    type="body1"
                                    component="p"
                                    gutterBottom
                                >
                                    {translate(
                                        'ah.promos.explain.download_promo_pack'
                                    )}
                                </Typography>
                                <Button
                                    color="accent"
                                    raised
                                    href={downloadUrl}
                                >
                                    {translate(
                                        'ah.promos.button.download_promo_pack'
                                    )}
                                </Button>
                            </DialogContent>
                            <DialogActions>
                                <Button
                                    onClick={this.handleCloseDialog}
                                    color="primary"
                                >
                                    {translate('ah.promos.close')}
                                </Button>
                            </DialogActions>
                        </div>
                    )}
                </Dialog>
            </div>
        );
    }
}

Promo.propTypes = {
    id: PropTypes.string.isRequired,
    recipientId: PropTypes.string.isRequired,
    data: PropTypes.object.isRequired,
    match: PropTypes.object.isRequired,
    translate: PropTypes.func.isRequired,
    fetchData: PropTypes.func.isRequired,
    submittingFeedback: PropTypes.bool.isRequired,
    downloadUrl: PropTypes.string,
};

const mapStateToProps = (state, props) => {
    const id = decodeURIComponent(props.match.params.id);
    const recipientId = decodeURIComponent(props.match.params.recipientId);

    return {
        id,
        recipientId,
        data: state.ah_core.data.get(id) || {},
        submittingFeedback: state.ah_promo.submittingFeedback,
        downloadUrl: state.ah_promo.downloadUrl,
    };
};

const enhance = compose(
    translate,
    connect(mapStateToProps, {
        fetchData: fetchDataAction,
        submitPromoFeedback: submitPromoFeedbackAction,
        setTitle: setTitleAction,
        recordActivity: recordActivityAction,
    })
);

const EnhancedPromo = enhance(Promo);

export default EnhancedPromo;
