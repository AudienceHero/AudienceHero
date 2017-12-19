import React from 'react';
import PropTypes from 'prop-types';
import { CircularProgress } from 'material-ui/Progress';
import compose from 'recompose/compose';
import { FormControl, FormHelperText } from 'material-ui/Form';
import { translate } from '@audiencehero-frontoffice/core';
import { TextField, Typography } from 'material-ui';
import Select from 'material-ui/Select';
import Input, { InputLabel } from 'material-ui/Input';
import Button from 'material-ui/Button';
import { Form, Field, reduxForm } from 'redux-form';
import {
    DialogActions,
    DialogContent,
    DialogContentText,
    DialogTitle,
} from 'material-ui/Dialog';
import { withStyles } from 'material-ui/styles';

const styles = theme => ({
    formControl: {
        display: 'block',
        marginTop: theme.spacing.unit,
        marginBottom: theme.spacing.unit,
        minWidth: 120,
    },
    select: {
        width: '100%',
    },
});

export class BaseRatingField extends React.Component {
    render() {
        const {
            classes,
            label,
            input: { value, onChange },
            meta: { touched, error },
        } = this.props;

        return (
            <FormControl
                className={classes.formControl}
                error={touched && error ? true : false}
            >
                <InputLabel htmlFor="rating">{label}</InputLabel>
                <Select
                    className={classes.select}
                    native
                    input={<Input id="rating" />}
                    value={value}
                    onChange={onChange}
                >
                    {[
                        '',
                        '*',
                        '**',
                        '***',
                        '****',
                        '*****',
                    ].map((rating, index) => {
                        return (
                            <option key={index} value={index}>
                                {rating}
                            </option>
                        );
                    })}
                </Select>
            </FormControl>
        );
    }
}

const RatingField = withStyles(styles)(BaseRatingField);

export class BaseFavoriteTrackField extends React.Component {
    render() {
        const {
            classes,
            label,
            tracks,
            input: { value, onChange },
            meta: { touched, error },
        } = this.props;
        return (
            <FormControl
                className={classes.formControl}
                error={touched && error ? true : false}
            >
                <InputLabel htmlFor="favoriteTrack">{label}</InputLabel>
                <Select
                    className={classes.select}
                    native
                    input={<Input id="favoriteTrack" />}
                    value={value}
                    onChange={onChange}
                >
                    <option key="empty" value={null} />
                    {tracks.map((track, index) => {
                        return (
                            <option key={index} value={track['@id']}>
                                {track.title}
                            </option>
                        );
                    })}
                </Select>
            </FormControl>
        );
    }
}

const FavoriteTrackField = withStyles(styles)(BaseFavoriteTrackField);

export class FeedbackField extends React.Component {
    render() {
        const {
            label,
            helperText,
            input: { value, onChange },
            meta: { touched, error },
        } = this.props;

        return (
            <TextField
                id="placeholder"
                label={label}
                helperText={helperText}
                multiline
                fullWidth
                margin="normal"
                value={value}
                onChange={onChange}
            />
        );
    }
}

export class PromoFeedbackForm extends React.Component {
    render() {
        const {
            handleSubmit,
            classes,
            isSubmitting,
            tracks,
            translate,
        } = this.props;
        return (
            <form onSubmit={handleSubmit}>
                <DialogTitle>{translate('ah.promos.dialog.title')}</DialogTitle>
                <DialogContent>
                    {isSubmitting ? (
                        <CircularProgress
                            color="accent"
                            className={classes.progress}
                        />
                    ) : (
                        <div>
                            <Typography type="subheading">
                                {translate('ah.promos.form_explain')}
                            </Typography>
                            <Field
                                name="rating"
                                component={RatingField}
                                props={{ label: translate('ah.promos.rating') }}
                            />
                            <Field
                                name="favoriteTrack"
                                component={FavoriteTrackField}
                                props={{
                                    tracks,
                                    label: translate(
                                        'ah.promos.favorite_track'
                                    ),
                                }}
                            />
                            <Field
                                name="feedback"
                                component={FeedbackField}
                                props={{
                                    label: translate('ah.promos.feedback'),
                                    helperText: translate(
                                        'ah.promos.explain.feedback'
                                    ),
                                }}
                            />
                        </div>
                    )}
                </DialogContent>
                <DialogActions>
                    <Button
                        onClick={this.handleCloseDialog}
                        disabled={isSubmitting}
                        color="primary"
                    >
                        {translate('ah.promos.cancel')}
                    </Button>
                    <Button
                        onClick={handleSubmit}
                        raised
                        disabled={isSubmitting}
                        color="primary"
                    >
                        {translate('ah.promos.download')}
                    </Button>
                </DialogActions>
            </form>
        );
    }
}

PromoFeedbackForm.propTypes = {
    translate: PropTypes.func.isRequired,
    onSubmit: PropTypes.func.isRequired,
    handleSubmit: PropTypes.func.isRequired,
    tracks: PropTypes.array.isRequired,
    isSubmitting: PropTypes.bool.isRequired,
};

const enhance = compose(
    translate,
    reduxForm({ form: 'promo-feedback-form' }),
    withStyles(styles)
);

const EnhancedPromoFeedbackForm = enhance(PromoFeedbackForm);

EnhancedPromoFeedbackForm.defaultProps = {
    isSubmitting: false,
};

export default EnhancedPromoFeedbackForm;
