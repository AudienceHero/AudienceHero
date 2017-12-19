import React from 'react';
import { connect } from 'react-redux';
import PropTypes from 'prop-types';
import compose from 'recompose/compose';
import Typography from 'material-ui/Typography';
import { LinearProgress } from 'material-ui/Progress';
import Divider from 'material-ui/Divider';
import Button from 'material-ui/Button';
import SkipPreviousIcon from 'material-ui-icons/SkipPrevious';
import PlayArrowIcon from 'material-ui-icons/PlayArrow';
import PauseIcon from 'material-ui-icons/Pause';
import SkipNextIcon from 'material-ui-icons/SkipNext';
import ShareIcon from 'material-ui-icons/Share';
import IconButton from 'material-ui/IconButton';
import VolumeUpIcon from 'material-ui-icons/VolumeUp';
import VolumeMuteIcon from 'material-ui-icons/VolumeMute';
import Card, {
    CardActions,
    CardContent,
    CardMedia,
} from 'material-ui/Card';
import List, {
    ListItem,
    ListItemIcon,
    ListItemText,
} from 'material-ui/List';
import withWidth from 'material-ui/utils/withWidth';
import { withStyles } from 'material-ui/styles';
import Hidden from 'material-ui/Hidden';
import Slider from 'rc-slider';
import { stringify } from 'qs';
import {
    skipPrevious as skipPreviousAction,
    skipNext as skipNextAction,
    togglePlayPause as togglePlayPauseAction,
    playTrack as playTrackAction,
    initPlayer as initPlayerAction,
    trackSkip as trackSkipAction,
    silencePlayer as silencePlayerAction,
} from './actions/playerActions';
import { OrderedMap } from 'immutable';
import get from 'lodash.get';
import {
    ShareDialog,
    buildImgSrc,
    translate,
} from '@audiencehero-frontoffice/core';
import { Helmet } from 'react-helmet';

const styles = theme => {
    return {
        progress: {
            cursor: 'pointer',
        },
        playerTop: {
            backgroundColor: theme.palette.grey[200],
        },
        playerActions: {
            backgroundColor: theme.palette.grey[600],
        },
        red: {
            fill: 'red',
        },
        playIcon: {
            height: theme.spacing.unit * 4,
            width: theme.spacing.unit * 4,
        },
        description: {
            marginTop: theme.spacing.unit * 2,
        },
        [theme.breakpoints.up('sm')]: {
            player: {},
            playerTop: {
                display: 'flex',
                justifyContent: 'space-between',
            },
            playerDetails: {
                width: '50%',
                display: 'flex',
                flexFlow: 'column',
            },
            playerDetailsContent: {
                flex: 1,
                display: 'block',
            },
            playerSpacing: {
                padding: theme.spacing.unit * 2,
            },
            playerActions: {
                padding: theme.spacing.unit * 2,
                textAlign: 'center',
                justifyContent: 'center',
            },
            playerCover: {
                display: 'flex',
                height: 450,
                width: '50%',
            },
        },
        [theme.breakpoints.down('sm')]: {
            playerTop: {
                display: 'flex',
                flexDirection: 'column-reverse',
            },
            playerDetails: {
                textAlign: 'center',
            },
            playerDetailsContent: {
                marginBottom: theme.spacing.unit * 2,
            },
            playerActions: {
                justifyContent: 'center',
            },
            playerCover: {
                backgroundSize: 'contain',
                backgroundPosition: 'center',
                width: '100%',
                height: 200,
            },
        },
    };
};

export class Player extends React.Component {
    state = {
        shareDialogOpen: false,
        player: null,
    };

    handleCloseShareDialog = () => {
        this.setState({ shareDialogOpen: false });
    };

    handleOpenShareDialog = () => {
        this.setState({ shareDialogOpen: true });
    };

    handlePrevious = () => {
        this.props.skipPrevious({
            currentTrack: this.props.currentTrack,
            playlist: this.props.playlist,
        });
    };

    handleNext = () => {
        this.props.skipNext({
            currentTrack: this.props.currentTrack,
            playlist: this.props.playlist,
        });
    };

    handlePlayPause = () => {
        this.props.togglePlayPause({ currentTrack: this.props.currentTrack });
    };

    handlePlayTrack = track => {
        this.props.playTrack({
            currentTrack: this.props.currentTrack,
            playlist: this.props.playlist,
            track,
        });
    };

    handleTrackSkip = event => {
        const currentSound = this.props.currentSound;
        if (null == currentSound) {
            return;
        }

        const position =
            event.nativeEvent.layerX /
            event.nativeEvent.target.clientWidth *
            currentSound.durationEstimate;
        this.props.trackSkip({ position, track: this.props.currentTrack });
    };

    componentDidMount() {
        if (true === this.props.ready) {
            this.props.initPlayer(this.props.player, false);
        }
    }

    componentWillUnmount() {
        this.props.silencePlayer();
    }

    componentWillReceiveProps(nextProps) {
        if (get(this.props.player, '@id') !== get(nextProps.player, '@id')) {
            // Disable autoplay on mobile
            let autoplay = nextProps.autoplay;
            if (
                (autoplay &&
                    /(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|ipad|iris|kindle|Android|Silk|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i.test(
                        navigator.userAgent
                    )) ||
                /1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw-(n|u)|c55\/|capi|ccwa|cdm-|cell|chtm|cldc|cmd-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc-s|devi|dica|dmob|do(c|p)o|ds(12|-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(-|_)|g1 u|g560|gene|gf-5|g-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd-(m|p|t)|hei-|hi(pt|ta)|hp( i|ip)|hs-c|ht(c(-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i-(20|go|ma)|i230|iac( |-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|-[a-w])|libw|lynx|m1-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|-([1-8]|c))|phil|pire|pl(ay|uc)|pn-2|po(ck|rt|se)|prox|psio|pt-g|qa-a|qc(07|12|21|32|60|-[2-7]|i-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h-|oo|p-)|sdk\/|se(c(-|0|1)|47|mc|nd|ri)|sgh-|shar|sie(-|m)|sk-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h-|v-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl-|tdg-|tel(i|m)|tim-|t-mo|to(pl|sh)|ts(70|m-|m3|m5)|tx-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas-|your|zeto|zte-/i.test(
                    navigator.userAgent.substr(0, 4)
                )
            ) {
                autoplay = false;
            }

            this.props.initPlayer(nextProps.player, autoplay);
        }
    }

    render() {
        const {
            playProgress,
            shareUrl,
            artwork,
            disablePreviousButton,
            disableNextButton,
            player,
            buttonLabel,
            translate,
            classes,
            enableShare,
            currentTime,
            totalTime,
            description,
            playTrack,
            isPaused,
            currentTrack,
        } = this.props;
        const tracks = player.tracks || [];
        const playerTitle = get(player, 'title');

        if (!this.props.ready) {
            return <div />;
        }

        return (
            <Card className={classes.player}>
                <Helmet>
                    <title>
                        {get(currentTrack, 'title') + ' – ' + playerTitle}
                    </title>
                </Helmet>
                <div className={classes.playerTop}>
                    <div className={classes.playerDetails}>
                        <CardContent className={classes.playerDetailsContent}>
                            <Hidden smDown implementation="css">
                                <div className={classes.playerSpacing}>
                                    {
                                        <Typography
                                            type="display1"
                                            component="h1"
                                        >
                                            {player.title}
                                        </Typography>
                                    }
                                </div>
                            </Hidden>
                            <div className={classes.playerSpacing}>
                                <Typography type="headline" component="h2">
                                    {get(currentTrack, 'title')}
                                </Typography>
                            </div>
                            <div className={classes.playerSpacing}>
                                <Typography type="subheading" component="h2">
                                    <span style={{ float: 'left' }}>
                                        {get(currentTrack, 'position') + 1} /{' '}
                                        {tracks.length}
                                    </span>
                                    <span style={{ float: 'right' }}>
                                        <Duration duration={currentTime} /> /{' '}
                                        <Duration duration={totalTime} />
                                    </span>
                                </Typography>
                            </div>
                        </CardContent>
                        <CardActions className={classes.playerActions}>
                            <IconButton
                                color="contrast"
                                aria-label="Previous"
                                disabled={disablePreviousButton}
                                onClick={this.handlePrevious}
                            >
                                <SkipPreviousIcon />
                            </IconButton>
                            <IconButton
                                color="contrast"
                                aria-label="Play/pause"
                                onClick={this.handlePlayPause}
                            >
                                {isPaused && (
                                    <PlayArrowIcon
                                        className={classes.playIcon}
                                    />
                                )}
                                {!isPaused && (
                                    <PauseIcon className={classes.playIcon} />
                                )}
                            </IconButton>
                            <IconButton
                                color="contrast"
                                aria-label="Next"
                                disabled={disableNextButton}
                                onClick={this.handleNext}
                            >
                                <SkipNextIcon />
                            </IconButton>
                            {enableShare && (
                                <IconButton
                                    color="contrast"
                                    aria-label="Share"
                                    onClick={this.handleOpenShareDialog}
                                >
                                    <ShareIcon />
                                </IconButton>
                            )}
                            <Hidden mdDown implementation="css">
                                <Button
                                    raised
                                    dense
                                    color="primary"
                                    onClick={this.props.onButtonClick}
                                >
                                    {translate(buttonLabel)}
                                </Button>
                            </Hidden>
                        </CardActions>
                        <Hidden smUp>
                            <CardActions className={classes.playerActions}>
                                <Button
                                    raised
                                    dense
                                    color="primary"
                                    onClick={this.props.onButtonClick}
                                >
                                    {buttonLabel}
                                </Button>
                            </CardActions>
                        </Hidden>
                    </div>
                    <CardMedia
                        className={classes.playerCover}
                        image={buildImgSrc({
                            url: artwork.remoteUrl,
                            size: '0x450',
                            crop: 'square-center',
                        })}
                        title={player.title}
                    />
                </div>
                <Slider min={0} max={100} step={0.1} />
                <LinearProgress
                    className={classes.progress}
                    mode="determinate"
                    color="accent"
                    value={playProgress}
                    onClick={this.handleTrackSkip}
                />
                {description &&
                    tracks.length > 0 && (
                        <CardContent>
                            {tracks.length > 0 && (
                                <div>
                                    <List disablePadding>
                                        {tracks.map((track, index) => (
                                            <ListItem
                                                key={index}
                                                button
                                                onClick={() => {
                                                    this.handlePlayTrack(track);
                                                }}
                                            >
                                                {get(track, '@id') ===
                                                    get(
                                                        currentTrack,
                                                        '@id'
                                                    ) && (
                                                    <ListItemIcon>
                                                        {isPaused ? (
                                                            <VolumeMuteIcon />
                                                        ) : (
                                                            <VolumeUpIcon />
                                                        )}
                                                    </ListItemIcon>
                                                )}
                                                <ListItemText
                                                    inset
                                                    primary={track.title}
                                                />
                                            </ListItem>
                                        ))}
                                    </List>
                                    <Divider />
                                </div>
                            )}
                            {description && (
                                <Typography
                                    type="subheading"
                                    component="p"
                                    className={classes.description}
                                >
                                    {description}
                                </Typography>
                            )}
                        </CardContent>
                    )}
                {enableShare && (
                    <ShareDialog
                        open={this.state.shareDialogOpen}
                        shareUrl={shareUrl}
                        title={playerTitle}
                        onRequestClose={this.handleCloseShareDialog}
                    />
                )}
            </Card>
        );
    }
}

Player.propTypes = {
    enableShare: PropTypes.bool.isRequired,
    classNames: PropTypes.object,
    player: PropTypes.object.isRequired,
    onButtonClick: PropTypes.func,
    description: PropTypes.string,
    skipPrevious: PropTypes.func.isRequired,
    skipNext: PropTypes.func.isRequired,
    togglePlayPause: PropTypes.func.isRequired,
    playTrack: PropTypes.func.isRequired,
    autoplay: PropTypes.bool,
    artwork: PropTypes.object.isRequired,
    currentTrack: PropTypes.object,
    currentSound: PropTypes.object,
    playlist: PropTypes.instanceOf(OrderedMap),
    playProgress: PropTypes.number,
    isLoading: PropTypes.bool,
    totalTime: PropTypes.number,
    currentTime: PropTypes.number,
    disablePreviousButton: PropTypes.bool,
    disableNextButton: PropTypes.bool,
    isPaused: PropTypes.bool,
    ready: PropTypes.bool.isRequired,
    shareUrl: PropTypes.string,
};

export const Duration = ({ duration }) => {
    var minutes = Math.floor(duration / 60000);
    var seconds = ((duration % 60000) / 1000).toFixed(0);

    return <span>{minutes + ':' + (seconds < 10 ? '0' : '') + seconds}</span>;
};

const mapStateToProps = (state, props) => {
    const currentlyLoaded = state.ah_file.player.currentlyLoaded;
    const playlist = state.ah_file.player.playlist;
    const loadedSounds = state.ah_file.player.loadedSounds;

    const id = get(currentlyLoaded, '@id');
    var currentSound = loadedSounds.get(get(currentlyLoaded, '@id'));
    var currentTrack = playlist.get(get(currentlyLoaded, '@id'));

    if (typeof currentSound === 'undefined') {
        currentSound = null;
    }
    if (typeof currentTrack === 'undefined') {
        currentTrack = null;
    }

    const totalTime = currentSound !== null ? currentSound.durationEstimate : 0;
    const currentTime = currentSound !== null ? currentSound.position : 0;
    const playProgress =
        currentSound !== null
            ? currentSound.position / currentSound.durationEstimate * 100
            : 0;

    const disablePreviousButton =
        currentTrack !== null ? currentTrack.position == 0 : true;
    const disableNextButton =
        currentTrack !== null
            ? currentTrack.position + 1 === playlist.size
            : true;
    var isPaused = true;
    if (currentSound !== null) {
        isPaused =
            currentSound.readyState != 3 || currentSound.playState == 0
                ? true
                : currentSound.paused;
    }

    const mappedProps = {
        currentTrack,
        currentSound,
        playlist,
        playProgress,
        totalTime,
        currentTime,
        disablePreviousButton,
        disableNextButton,
        isPaused,
    };

    return mappedProps;
};

const EnhancedPlayer = compose(
    connect(mapStateToProps, {
        skipPrevious: skipPreviousAction,
        skipNext: skipNextAction,
        togglePlayPause: togglePlayPauseAction,
        playTrack: playTrackAction,
        initPlayer: initPlayerAction,
        trackSkip: trackSkipAction,
        silencePlayer: silencePlayerAction,
    }),
    translate,
    withStyles(styles),
    withWidth()
)(Player);

EnhancedPlayer.defaultProps = {
    enableShare: true,
    buttonLabel: 'ah.file.action.download',
    autoplay: false,
    ready: false,
};

export default EnhancedPlayer;
