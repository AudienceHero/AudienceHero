import PropTypes from 'prop-types';
import React from 'react';
import { connect } from 'react-redux';
import compose from 'recompose/compose';
import { Player } from '@audiencehero-frontoffice/file';
import { translate } from '@audiencehero-frontoffice/core';
import {
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
import { unlockDownload as unlockDownloadAction } from './actions';
import UnlockForm from './UnlockForm';

export class AcquisitionFreeDownload extends React.Component {
    state = {
        open: false,
        ready: false,
    };

    componentDidMount() {
        // Fetch api resource
        this.updateData();
    }

    componentWillReceiveProps(nextProps) {
        const playerID = get(nextProps.data, '@id');
        if (typeof playerID !== 'undefined') {
            this.setState({ ready: true });
        }

        if (this.props.id !== nextProps.id) {
            this.updateData(nextProps.id, nextProps.isPreview);
            if (!nextProps.isPreview) {
            }
        }
    }

    updateData(id = this.props.id, isPreview = this.props.isPreview) {
        this.props.fetchData('acquisition_free_downloads', id, isPreview);
    }

    handleButtonClick = () => {
        this.setState({ open: true });
    };

    handleCloseDialog = () => {
        this.setState({ open: false });
    };

    handleSubmit = values => {
        this.props.unlockDownload({
            id: this.props.id,
            values,
            auth: this.props.isPreview,
        });
    };

    render() {
        const { unlocking, translate, data, downloadUrl } = this.props;
        const tracks = data.player ? data.player.tracks : [];
        const shareUrl = get(data, 'urls.public');

        return (
            <div>
                <Player
                    ready={this.state.ready}
                    shareUrl={shareUrl}
                    artwork={data.artwork || {}}
                    description={data.description}
                    player={data.player || {}}
                    enableShare={true}
                    autoplay={false}
                    onButtonClick={this.handleButtonClick}
                />
                <Dialog
                    open={this.state.open}
                    onRequestClose={this.handleCloseDialog}
                >
                    {null === downloadUrl ? (
                        <UnlockForm
                            isSubmitting={unlocking}
                            onSubmit={this.handleSubmit}
                            contactsGroupForm={data.contactsGroupForm || {}}
                        />
                    ) : (
                        <div>
                            <DialogTitle>
                                {translate('ah.afd.dialog.download.title')}
                            </DialogTitle>
                            <DialogContent>
                                <Typography
                                    type="body1"
                                    component="p"
                                    gutterBottom
                                >
                                    {translate(
                                        'ah.afd.dialog.download.explain'
                                    )}
                                </Typography>
                                <Button
                                    color="accent"
                                    raised
                                    href={downloadUrl}
                                >
                                    {translate(
                                        'ah.afd.dialog.download.button.download'
                                    )}
                                </Button>
                            </DialogContent>
                            <DialogActions>
                                <Button
                                    onClick={this.handleCloseDialog}
                                    color="primary"
                                >
                                    {translate('ah.core.dialog.button.close')}
                                </Button>
                            </DialogActions>
                        </div>
                    )}
                </Dialog>
            </div>
        );
    }
}

AcquisitionFreeDownload.propTypes = {
    id: PropTypes.string.isRequired,
    isPreview: PropTypes.bool.isRequired,
    data: PropTypes.object.isRequired,
    match: PropTypes.object.isRequired,
    translate: PropTypes.func.isRequired,
    fetchData: PropTypes.func.isRequired,
    unlocking: PropTypes.bool.isRequired,
    downloadUrl: PropTypes.string,
};

const mapStateToProps = (state, props) => {
    const id = decodeURIComponent(props.match.params.id);
    const isPreview = 'preview' === get(props.match.params, 'preview');

    return {
        id,
        isPreview,
        data: state.ah_core.data.get(id) || {},
        unlocking: state.ah_afd.unlocking,
        downloadUrl: state.ah_afd.downloadUrl,
    };
};

const enhance = compose(
    translate,
    connect(mapStateToProps, {
        fetchData: fetchDataAction,
        setTitle: setTitleAction,
        unlockDownload: unlockDownloadAction,
    })
);

const EnhancedAcquisitionFreeDownload = enhance(AcquisitionFreeDownload);

export default EnhancedAcquisitionFreeDownload;
