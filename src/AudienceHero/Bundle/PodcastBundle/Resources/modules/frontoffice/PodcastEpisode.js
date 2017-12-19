import PropTypes from 'prop-types';
import { Player } from '@audiencehero-frontoffice/file';
import { withStyles } from 'material-ui/styles';
import { connect } from 'react-redux';
import React from 'react';
import compose from 'recompose/compose';
import Button from 'material-ui/Button';
import get from 'lodash.get';
import {
    translate,
    setTitle as setTitleAction,
    fetchData as fetchDataAction,
} from '@audiencehero-frontoffice/core';
import List, {
    ListItem,
    ListItemIcon,
    ListItemText,
} from 'material-ui/List';
import AddIcon from 'material-ui-icons/AddCircle';
import RssIcon from 'material-ui-icons/RssFeed';
import SubscribeIcon from 'material-ui-icons/ContactMail';
import { push as pushAction } from 'react-router-redux';
import ChannelIcon from 'material-ui-icons/PlaylistPlay';
import Dialog, {
    DialogTitle,
    DialogContent,
    DialogActions,
} from 'material-ui/Dialog';

const styles = theme => {
    return {};
};

export class PodcastEpisode extends React.Component {
    state = {
        ready: false,
        open: false,
    };

    componentDidMount() {
        this.updateData();
    }

    componentWillReceiveProps(nextProps) {
        const id = get(nextProps, 'data.id');
        if (typeof id !== 'undefined') {
            this.setState({ ready: true });
            this.props.setTitle(get(nextProps.data, 'title'));
        } else {
            this.setState({ ready: false });
        }
    }

    updateData = (id = this.props.episodeId) => {
        this.props.fetchData('podcast_episodes', id, false);
    };

    openDialog = () => {
        this.setState({ open: true });
    };

    handleCloseDialog = () => {
        this.setState({ open: false });
    };

    render() {
        const { ready } = this.state;
        if (!ready) {
            return false;
        }
        const { classes, translate, push, data } = this.props;

        var artwork = data.artwork;
        if (null === artwork) {
            artwork = data.channel.artwork;
        }

        return (
            <div>
                <Player
                    buttonLabel="ah.podcast.button.subscribe"
                    onButtonClick={this.openDialog}
                    ready={this.state.ready}
                    shareUrl={data.urls.public}
                    artwork={artwork}
                    description={data.description}
                    player={data.player}
                    enableShare={true}
                    autoplay={false}
                />
                <Dialog
                    open={this.state.open}
                    onRequestClose={this.handleCloseDialog}
                >
                    <DialogTitle>
                        {translate('ah.podcast.dialog.subscribe.title')}
                    </DialogTitle>
                    <DialogContent>
                        <List>
                            <ListItem
                                button
                                onClick={() => {
                                    push(
                                        `/forms/${data.channel.contactsGroupForm
                                            .id}`
                                    );
                                }}
                            >
                                <ListItemIcon>
                                    <SubscribeIcon />
                                </ListItemIcon>
                                <ListItemText
                                    primary={translate(
                                        'ah.podcast.action.subscribe_to_mailing_list'
                                    )}
                                />
                            </ListItem>
                            <ListItem
                                button
                                onClick={() => {
                                    push(`/podcasts/${data.channel.id}`);
                                }}
                            >
                                <ListItemIcon>
                                    <ChannelIcon />
                                </ListItemIcon>
                                <ListItemText
                                    primary={translate(
                                        'ah.podcast.action.view_other_episodes'
                                    )}
                                />
                            </ListItem>
                            <ListItem
                                button
                                href={data.channel.urls.rss_feed.replace(
                                    /^https?/,
                                    'itpc'
                                )}
                            >
                                <ListItemIcon>
                                    <AddIcon />
                                </ListItemIcon>
                                <ListItemText
                                    primary={translate(
                                        'ah.podcast.action.subscribe_itunes'
                                    )}
                                />
                            </ListItem>
                            <ListItem
                                button
                                component="a"
                                target="_blank"
                                href={data.channel.urls.rss_feed}
                            >
                                <ListItemIcon>
                                    <RssIcon />
                                </ListItemIcon>
                                <ListItemText
                                    primary={translate(
                                        'ah.podcast.action.rss_feed'
                                    )}
                                />
                            </ListItem>
                        </List>
                    </DialogContent>
                    <DialogActions>
                        <Button
                            onClick={this.handleCloseDialog}
                            color="primary"
                        >
                            {translate('ah.core.dialog.button.close')}
                        </Button>
                    </DialogActions>
                </Dialog>
            </div>
        );
    }
}

PodcastEpisode.propTypes = {
    id: PropTypes.string.isRequired,
    episodeId: PropTypes.string.isRequired,
    data: PropTypes.object.isRequired,
    fetchData: PropTypes.func.isRequired,
    translate: PropTypes.func.isRequired,
    push: PropTypes.func.isRequired,
    classes: PropTypes.object.isRequired,
};

const mapStateToProps = (state, props) => {
    const id = decodeURIComponent(props.match.params.id);
    const episodeId = decodeURIComponent(props.match.params.episodeId);

    return {
        id,
        episodeId,
        data: state.ah_core.data.get(episodeId) || {},
    };
};

const enhance = compose(
    translate,
    connect(mapStateToProps, {
        fetchData: fetchDataAction,
        setTitle: setTitleAction,
        push: pushAction,
    }),
    withStyles(styles)
);

export default enhance(PodcastEpisode);
