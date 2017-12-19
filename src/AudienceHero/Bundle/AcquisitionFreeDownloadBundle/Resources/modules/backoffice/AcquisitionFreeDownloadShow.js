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
import CardShare from './CardShare';
import { crudGetList as crudGetListAction, translate } from 'react-admin';
import { connect } from 'react-redux';
import compose from 'recompose/compose';
import HitsIcon from 'material-ui-icons/People';
import UnlockIcon from 'material-ui-icons/LockOpen';
import get from 'lodash.get';
import {
    CardChart,
    CardShow,
    CardTable,
    CardRow,
    CardStat,
} from '@audiencehero-backoffice/core';
import {
    Bar,
    BarChart,
    CartesianGrid,
    ResponsiveContainer,
    Tooltip,
    XAxis,
    YAxis,
} from 'recharts';
import amber from 'material-ui/colors/amber';
const amber200 = amber['200'];
import {
    Table,
    TableBody,
    TableHeader,
    TableHeaderColumn,
    TableRow,
    TableRowColumn,
} from 'material-ui/Table';

export class AcquisitionFreeDownloadShow extends React.Component {
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
        this.props.crudGetList(
            'activities',
            pagination,
            sort,
            {
                type: 'acquisition_free_download.unlock',
                'subjects[acquisition_free_downloads]': `/api/${this.props
                    .resource}/${this.props.id}`,
            },
            false
        );
    };

    updateDailyHits = () => {
        const aggregates = this.props.aggregates;
        const rawDailyHits = get(
            aggregates['acquisition_free_download.hit'],
            'daily'
        );
        const dailyHits = [];
        rawDailyHits &&
            Object.keys(rawDailyHits).forEach((key, index) => {
                dailyHits.push({
                    date: key,
                    hits: rawDailyHits[key],
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
                        value={get(
                            aggregates['acquisition_free_download.hit'],
                            'total'
                        )}
                        icon={<HitsIcon />}
                        label="ah.afd.show.stats.hits"
                    />
                    <CardStat
                        value={get(
                            aggregates['acquisition_free_download.unlock'],
                            'total'
                        )}
                        icon={<UnlockIcon />}
                        label="ah.afd.show.stats.unlocks"
                    />
                </CardRow>
                <CardRow>
                    <CardChart label="ah.afd.show.chart.hits_by_day">
                        <ResponsiveContainer minHeight={250}>
                            <BarChart data={this.state.dailyHits}>
                                <XAxis dataKey="date" />
                                <YAxis />
                                <CartesianGrid strokeDasharray="3 3" />
                                <Tooltip />
                                <Bar
                                    type="monotone"
                                    dataKey="hits"
                                    fill={amber200}
                                />
                            </BarChart>
                        </ResponsiveContainer>
                    </CardChart>
                </CardRow>
                <CardRow>
                    <CardTable label={translate('ah.afd.show.table.unlocks')}>
                        <Table selectable={false}>
                            <TableHeader
                                adjustForCheckbox={false}
                                displaySelectAll={false}
                            >
                                <TableRow selectable={false}>
                                    <TableHeaderColumn>
                                        {translate(
                                            'ah.afd.show.table.header.name'
                                        )}
                                    </TableHeaderColumn>
                                    <TableHeaderColumn>
                                        {translate(
                                            'ah.afd.show.table.header.email'
                                        )}
                                    </TableHeaderColumn>
                                    <TableHeaderColumn>
                                        {translate(
                                            'ah.afd.show.table.header.phone'
                                        )}
                                    </TableHeaderColumn>
                                    <TableHeaderColumn />
                                </TableRow>
                            </TableHeader>
                            <TableBody displayRowCheckbox={false}>
                                {this.props.activities.map(
                                    (activity, index) => (
                                        <TableRow
                                            selectable={false}
                                            key={index}
                                        >
                                            <TableRowColumn>
                                                {get(
                                                    activity,
                                                    'subjects.contacts.name'
                                                )}
                                            </TableRowColumn>
                                            <TableRowColumn>
                                                {get(
                                                    activity,
                                                    'subjects.contacts.email'
                                                )}
                                            </TableRowColumn>
                                            <TableRowColumn>
                                                {get(
                                                    activity,
                                                    'subjects.contacts.phone'
                                                )}
                                            </TableRowColumn>
                                            <TableRowColumn>
                                                <ShowButton
                                                    basePath="/contacts"
                                                    record={get(
                                                        activity,
                                                        'subjects.contacts'
                                                    )}
                                                />
                                            </TableRowColumn>
                                        </TableRow>
                                    )
                                )}
                            </TableBody>
                        </Table>
                    </CardTable>
                </CardRow>
            </CardShow>
        );
    }
}

AcquisitionFreeDownloadShow.propTypes = {
    id: PropTypes.string.isRequired,
    aggregates: PropTypes.object.isRequired,
    activities: PropTypes.array.isRequired,
};

export const ShowTitle = ({ record }) => <span>{record.reference}</span>;

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

    const activities = [];
    for (const activity of Object.values(
        state.admin.resources.activities.data
    )) {
        // first, we skip every activities that are not related to what we want
        if ('acquisition_free_download.unlock' !== activity.type) {
            continue;
        }

        if (get(activity, 'subjects.acquisition_free_downloads.id') === id) {
            activities.push(activity);
        }
    }

    return {
        id,
        aggregates,
        activities,
    };
};

const enhance = compose(
    translate,
    connect(mapStateToProps, {
        crudGetList: crudGetListAction,
    })
);

export default enhance(AcquisitionFreeDownloadShow);
