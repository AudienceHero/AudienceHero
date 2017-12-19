import React from 'react';
import { Button, Paper, Typography } from 'material-ui';
import { withStyles } from 'material-ui/styles';
import translate from '../i18n/translate';
import compose from 'recompose/compose';

const styles = theme => ({
    paper: {
        padding: theme.spacing.unit * 4,
    },
    button: {
        marginTop: theme.spacing.unit * 2,
    },
});

export class Unauthorized extends React.Component {
    render() {
        const { classes, translate } = this.props;
        return (
            <Paper className={classes.paper}>
                <Typography
                    color="accent"
                    type="display1"
                    gutterBottom
                    component="h1"
                >
                    {translate('ah.error.unauthorized')}
                </Typography>
                <Typography type="body1" component="p">
                    {translate('ah.error.unauthorized.explain')}
                </Typography>
                <Button
                    className={classes.button}
                    color="primary"
                    raised
                    href="/admin/login"
                >
                    {translate('ah.button.login_to_your_account')}
                </Button>
            </Paper>
        );
    }
}

const enhance = compose(translate, withStyles(styles));

const EnhancedUnauthorized = enhance(Unauthorized);

export default EnhancedUnauthorized;
