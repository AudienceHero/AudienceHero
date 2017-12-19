import compose from 'recompose/compose';
import shouldUpdate from 'recompose/shouldUpdate';
import React, { Children, cloneElement } from 'react';
import PropTypes from 'prop-types';
import { translate } from 'react-admin';

export class Actions extends React.Component {
    render() {
        const { basePath, record, children } = this.props;
        return (
            <div>
                {Children.map(children, child => {
                    return cloneElement(child, { record, basePath });
                })}
            </div>
        );
    }
}

Actions.defaultProps = {
    basePath: '',
    label: 'ah.actions',
    record: {},
};

Actions.propTypes = {
    children: PropTypes.node,
    label: PropTypes.string,
    record: PropTypes.object,
    translate: PropTypes.func.isRequired,
};

const enhance = compose(
    shouldUpdate(
        (props, nextProps) =>
            (props.record && props.record.id !== nextProps.record.id) ||
            (props.record == null && nextProps.record != null)
    ),
    translate
);

export default enhance(Actions);
