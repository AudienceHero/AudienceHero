import React from 'react';
import PropTypes from 'prop-types';
import {
    Create,
    List,
    Datagrid,
    TextField,
    LongTextInput,
    ReferenceInput,
    EmailField,
    DateField,
    EditButton,
    ShowButton,
    TabbedForm,
    SelectInput,
    FormTab,
    Edit,
    TextInput,
} from 'react-admin';
import { DateTimeInput } from 'aor-datetime-input';
import {
    CardStat,
    CardChart,
    CardShow,
    CardTable,
    CardRow,
} from '@audiencehero-backoffice/core';
import CardShare from './CardShare';
import { crudGetList as crudGetListAction, translate } from 'react-admin';
import { connect } from 'react-redux';
import compose from 'recompose/compose';
import HitsIcon from 'material-ui-icons/People';
import UnlockIcon from 'material-ui-icons/LockOpen';
import RssFeedIcon from 'material-ui-icons/RssFeed';
import EnclosureDownloadIcon from 'material-ui-icons/CloudDownload';
import get from 'lodash.get';
import {
    Bar,
    BarChart,
    Line,
    LineChart,
    CartesianGrid,
    ResponsiveContainer,
    Legend,
    Tooltip,
    XAxis,
    YAxis,
} from 'recharts';
import amber from 'material-ui/colors/amber';
import blue from 'material-ui/colors/blue';
import purple from 'material-ui/colors/purple';
import pink from 'material-ui/colors/pink';
const amber200 = amber['200'];
const blue200 = blue['200'];
const purple200 = purple['200'];
const pink200 = pink['200'];
import {
    Table,
    TableBody,
    TableHeader,
    TableHeaderColumn,
    TableRow,
    TableRowColumn,
} from 'material-ui/Table';

export class PodcastChannelShow extends React.Component {
    state = {
        dailyHits: [],
    };

    componentDidMount() {
        this.updateData();
    }

    componentWillReceiveProps(nextProps) {
        if (this.props.id !== nextProps.id) {
            this.updateData();
        }

        this.updateDailyHits();
    }

    updateData = () => {
        const pagination = { page: 1, perPage: 100 };
        const sort = { field: 'createdAt', order: 'desc' };
        this.props.crudGetList(
            'aggregates',
            pagination,
            sort,
            { subjectId: this.props.id },
            false
        );
    };

    updateDailyHits = () => {
        const aggregates = this.props.aggregates;
        const rawDailyHits = get(aggregates['podcast_channel.hit'], 'daily');
        const rawDailyFeedHits = get(
            aggregates['podcast_channel.feed_hit'],
            'daily'
        );
        const rawDailyEpisodeHits = get(
            aggregates['podcast_episode.hit'],
            'daily'
        );
        const rawDailyEnclosureDownloads = get(
            aggregates['podcast_episode.enclosure_downloads'],
            'daily'
        );
        const dailyHits = [];
        rawDailyHits &&
            Object.keys(rawDailyHits).forEach((key, index) => {
                dailyHits.push({
                    date: key,
                    hits: rawDailyHits ? rawDailyHits[key] : 0,
                    feed_hits: rawDailyFeedHits ? rawDailyFeedHits[key] : 0,
                    total_episode_hits: rawDailyEpisodeHits
                        ? rawDailyEpisodeHits[key]
                        : 0,
                    enclosure_downloads: rawDailyEnclosureDownloads
                        ? rawDailyEnclosureDownloads[key]
                        : 0,
                });
            });
        this.setState({ dailyHits });
    };

    render() {
        const props = this.props;
        const translate = props.translate;
        const aggregates = props.aggregates;

        return (
            <CardShow {...props} title={<ShowTitle />}>
                <CardShare />
                <CardRow>
                    <CardStat
                        xs={6}
                        sm={6}
                        md={3}
                        lg={3}
                        value={get(aggregates['podcast_channel.hit'], 'total')}
                        icon={<HitsIcon />}
                        label="ah.podcast.show.stats.hits"
                    />
                    <CardStat
                        xs={6}
                        sm={6}
                        md={3}
                        lg={3}
                        value={get(
                            aggregates['podcast_channel.feed_hit'],
                            'total'
                        )}
                        icon={<RssFeedIcon />}
                        label="ah.podcast.show.stats.feed_hits"
                    />
                    <CardStat
                        xs={6}
                        sm={6}
                        md={3}
                        lg={3}
                        value={get(aggregates['podcast_episode.hit'], 'total')}
                        icon={<HitsIcon />}
                        label="ah.podcast.show.stats.total_episode_hits"
                    />
                    <CardStat
                        xs={6}
                        sm={6}
                        md={3}
                        lg={3}
                        value={get(
                            aggregates['podcast_episode.enclosure_download'],
                            'total'
                        )}
                        icon={<EnclosureDownloadIcon />}
                        label="ah.podcast.show.stats.total_enclosure_downloads"
                    />
                </CardRow>
                <CardRow>
                    <CardChart label="ah.podcast.show.chart.hits_by_day">
                        <ResponsiveContainer minHeight={250}>
                            <BarChart data={this.state.dailyHits}>
                                <XAxis dataKey="date" />
                                <YAxis />
                                <CartesianGrid strokeDasharray="3 3" />
                                <Tooltip />
                                <Bar
                                    type="monotone"
                                    label={translate(
                                        'ah.podcast.show.chart.label.hits'
                                    )}
                                    dataKey="hits"
                                    fill={amber200}
                                />
                                <Bar
                                    type="monotone"
                                    label={translate(
                                        'ah.podcast.show.chart.label.feed_hits'
                                    )}
                                    dataKey="feed_hits"
                                    fill={blue200}
                                />
                                <Bar
                                    type="monotone"
                                    label={translate(
                                        'ah.podcast.show.chart.label.total_episode_hits'
                                    )}
                                    dataKey="episode_hits"
                                    fill={pink200}
                                />
                                <Bar
                                    type="monotone"
                                    label={translate(
                                        'ah.podcast.show.chart.label.total_enclosure_downloads'
                                    )}
                                    dataKey="enclosure_downloads"
                                    fill={purple200}
                                />
                                <Legend />
                            </BarChart>
                        </ResponsiveContainer>
                    </CardChart>
                </CardRow>
            </CardShow>
        );
    }
}

PodcastChannelShow.propTypes = {
    id: PropTypes.string.isRequired,
    aggregates: PropTypes.object.isRequired,
};

export const ShowTitle = ({ record }) => <span>{record.title}</span>;

const mapStateToProps = (state, props) => {
    const id = decodeURIComponent(props.match.params.id);

    // Gather all aggregates in one easy object to process
    const aggregates = {};
    for (const aggregate of Object.values(
        state.admin.resources.aggregates.data
    )) {
        if (id === aggregate.subjectId) {
            aggregates[aggregate.type] = aggregate.data;
        }
    }

    return {
        id,
        aggregates,
    };
};

const enhance = compose(
    translate,
    connect(mapStateToProps, {
        crudGetList: crudGetListAction,
    })
);

export default enhance(PodcastChannelShow);
