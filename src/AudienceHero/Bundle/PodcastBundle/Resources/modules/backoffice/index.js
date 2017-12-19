import {
    PodcastChannelCreate,
    PodcastChannelEdit,
    PodcastChannelList,
    PodcastEpisodeCreate,
    PodcastEpisodeEdit,
    PodcastEpisodeList,
} from './Podcast';
import { Delete } from 'react-admin';
import PodcastChannelIcon from 'material-ui-icons/RssFeed';
import PodcastEpisodeIcon from 'material-ui-icons/Mic';
import IconArrowDropRight from 'material-ui-icons/ArrowDropDown';
import messages from './messages';
import PodcastChannelShow from './PodcastChannelShow';
import PodcastEpisodeShow from './PodcastEpisodeShow';

export const Bundle = {
    messages,
    resources: [
        {
            name: 'podcast_channels',
            list: PodcastChannelList,
            create: PodcastChannelCreate,
            edit: PodcastChannelEdit,
            show: PodcastChannelShow,
            remove: Delete,
        },
        {
            name: 'podcast_episodes',
            list: PodcastEpisodeList,
            create: PodcastEpisodeCreate,
            edit: PodcastEpisodeEdit,
            show: PodcastEpisodeShow,
            remove: Delete,
        },
    ],
    menu: [
        {
            primaryText: 'ah.podcast.menu.root',
            rightIcon: IconArrowDropRight,
            leftIcon: PodcastEpisodeIcon,
            menuItems: [
                {
                    leftIcon: PodcastChannelIcon,
                    to: '/podcast_channels',
                    primaryText: 'ah.podcast.menu.channels',
                },
                {
                    leftIcon: PodcastEpisodeIcon,
                    to: '/podcast_episodes',
                    primaryText: 'ah.podcast.menu.episodes',
                },
            ],
        },
    ],
};
