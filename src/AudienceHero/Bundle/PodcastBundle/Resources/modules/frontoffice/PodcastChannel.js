import PropTypes from 'prop-types';
import { withStyles } from 'material-ui/styles';
import Hidden from 'material-ui/Hidden';
import { connect } from 'react-redux';
import React from 'react';
import compose from 'recompose/compose';
import Button from 'material-ui/Button';
import IconButton from 'material-ui/IconButton';
import Card, {
    CardActions,
    CardContent,
    CardMedia,
} from 'material-ui/Card';
import Typography from 'material-ui/Typography';
import get from 'lodash.get';
import List, {
    ListItem,
    ListItemIcon,
    ListItemText,
} from 'material-ui/List';
import PlayIcon from 'material-ui-icons/PlayCircleFilled';
import ShareIcon from 'material-ui-icons/Share';
import RssIcon from 'material-ui-icons/RssFeed';
import SubscribeIcon from 'material-ui-icons/ContactMail';
import { push as pushAction } from 'react-router-redux';
import {
    buildImgSrc,
    setTitle as setTitleAction,
    fetchData as fetchDataAction,
    translate,
    ShareDialog,
} from '@audiencehero-frontoffice/core';

const styles = theme => {
    return {
        top: {
            display: 'flex',
        },
        [theme.breakpoints.up('sm')]: {
            top: {
                justifyContent: 'space-between',
            },
            details: {
                width: '50%',
                display: 'flex',
                flexFlow: 'column',
            },
            media: {
                display: 'flex',
                height: 450,
                width: '50%',
            },
        },
        [theme.breakpoints.down('sm')]: {
            top: {
                flexDirection: 'column-reverse',
            },
            details: {
                textAlign: 'center',
            },
            media: {
                backgroundSize: 'contain',
                backgroundPosition: 'center',
                width: '100%',
                height: 200,
            },
            actions: {
                justifyContent: 'center',
            },
        },
    };
};

export class PodcastChannel extends React.Component {
    state = {
        ready: false,
        shareDialogOpen: false,
    };

    componentDidMount() {
        this.updateData();
    }

    componentWillReceiveProps(nextProps) {
        const id = get(nextProps, 'data.id');
        if (id) {
            this.setState({ ready: true });
            this.props.setTitle(get(nextProps.data, 'title'));
        } else {
            this.setState({ ready: false });
        }
    }

    updateData = (id = this.props.id) => {
        this.props.fetchData('podcast_channels', id, false);
    };

    closeShareDialog = () => {
        this.setState({ shareDialogOpen: false });
    };

    openShareDialog = () => {
        this.setState({ shareDialogOpen: true });
    };

    render() {
        const { ready } = this.state;
        if (!ready) {
            return false;
        }

        const { classes, translate, push, data } = this.props;

        const id = get(data, 'id');
        const rssFeed = get(data, 'urls.rss_feed');
        const episodes = get(data, 'publicEpisodes') || {};

        return (
            <Card>
                <ShareDialog
                    onRequestClose={this.closeShareDialog}
                    open={this.state.shareDialogOpen}
                    shareUrl={get(data, 'urls.public')}
                    title={data.title}
                />
                <div className={classes.top}>
                    <CardContent className={classes.details}>
                        <Hidden mdDown>
                            <Typography type="display1" component="h1">
                                {data.title}
                            </Typography>
                        </Hidden>
                        <Typography type="headline" component="h2">
                            {data.subtitle}
                        </Typography>
                        <Typography type="subheading" component="p">
                            {data.description}
                        </Typography>
                    </CardContent>
                    <CardMedia
                        className={classes.media}
                        title={data.title}
                        image={buildImgSrc({
                            url: data.artwork.remoteUrl,
                            size: '0x450',
                            crop: 'square-center',
                        })}
                    />
                </div>
                <CardActions className={classes.actions}>
                    <Hidden smDown>
                        <Button
                            color="primary"
                            raised
                            href={rssFeed.replace(/^https?/, 'itpc')}
                            target="_blank"
                        >
                            {translate('ah.podcast.button.itunes')}
                        </Button>
                    </Hidden>
                    <Hidden smUp>
                        <IconButton>
                            <SubscribeIcon />
                        </IconButton>
                    </Hidden>
                    <IconButton href={rssFeed} target="_blank">
                        <RssIcon />
                    </IconButton>
                    <IconButton onClick={this.openShareDialog}>
                        <ShareIcon />
                    </IconButton>
                    <IconButton>
                        <SubscribeIcon
                            onClick={() => {
                                push(`/forms/${data.contactsGroupForm.id}`);
                            }}
                        />
                    </IconButton>
                </CardActions>
                <CardContent>
                    <Typography type="headline" component="h2">
                        {translate('ah.podcast.title.episodes')}
                    </Typography>
                    <List>
                        {Object.values(episodes).map(episode => {
                            return (
                                <ListItem
                                    key={episode.id}
                                    button
                                    onClick={() => {
                                        push(`/podcasts/${id}/${episode.id}`);
                                    }}
                                >
                                    <ListItemIcon>
                                        <PlayIcon />
                                    </ListItemIcon>
                                    <ListItemText
                                        primary={episode.title}
                                        secondary={episode.subtitle}
                                    />
                                </ListItem>
                            );
                        })}
                    </List>
                </CardContent>
            </Card>
        );
    }
}

PodcastChannel.propTypes = {
    id: PropTypes.string.isRequired,
    data: PropTypes.object.isRequired,
    fetchData: PropTypes.func.isRequired,
    translate: PropTypes.func.isRequired,
    push: PropTypes.func.isRequired,
    classes: PropTypes.object.isRequired,
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
        push: pushAction,
        setTitle: setTitleAction,
    }),
    withStyles(styles)
);

export default enhance(PodcastChannel);
