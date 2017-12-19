// Taken from material-ui.
// @see https://github.com/callemall/material-ui/blob/v1-beta/docs/src/modules/components/AppContent.js

import React from 'react';
import PropTypes from 'prop-types';
import classNames from 'classnames';
import { withStyles } from 'material-ui/styles';

const styles = theme => ({
    content: theme.mixins.gutters({
        paddingTop: '2em',
        paddingBottom: '2em',
        flex: '1 1 100%',
        maxWidth: '100%',
        margin: '0 auto',
    }),
    [theme.breakpoints.down('md')]: {
        content: {
            paddingTop: '1em',
        },
    },
    [theme.breakpoints.up('md')]: {
        content: {
            maxWidth: 900,
        },
    },
});

function Wrapper(props) {
    const { className, classes, children } = props;

    return (
        <div className={classNames(classes.content, className)}>{children}</div>
    );
}

Wrapper.propTypes = {
    children: PropTypes.node.isRequired,
    classes: PropTypes.object.isRequired,
    className: PropTypes.string,
};

export default withStyles(styles)(Wrapper);
