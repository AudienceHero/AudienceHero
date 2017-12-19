import React from 'react';
import PropTypes from 'prop-types';
import { Labeled, translate } from 'react-admin';
import { GridList, GridTile } from 'material-ui/GridList';
import Dialog from 'material-ui/Dialog';
import IconPhotoLibrary from 'material-ui-icons/PhotoLibrary';
import FileCard from './FileCard';
import Button from "material-ui/Button"
import get from 'lodash.get';

const contentStyle = {
    maxWidth: '90%',
    width: '99em',
};

const bodyStyle = {
    display: 'flex',
    flexDirection: 'row',
    flexWrap: 'wrap',
};

class DialogInput extends React.Component {
    constructor(props) {
        super(props);

        this.state = {
            open: false,
            choice: props.input.value,
        };
    }

    handleOpen = () => {
        this.setState({ open: true });
    };

    handleClose = () => {
        this.setState({ open: false });
    };

    handleChange = value => {
        this.setState({ open: false, choice: value });
        this.props.onFilePick(value);
        this.props.input.onChange(get(value, this.props.optionValue));
    };

    removeValue = () => {
        this.setState({ open: false, choice: null });
        this.props.input.onChange(null);
        this.props.onFilePick(null);
    };

    renderChoice = choice => {
        return (
            <FileCard
                key={choice['id']}
                record={choice}
                showActions={false}
                handleClick={record => {
                    this.handleChange(record);
                }}
            />
        );
    };

    render() {
        const { displayChoice, translate, choices, meta } = this.props;
        if (typeof meta === 'undefined') {
            throw new Error(
                "The DialogInput component wasn't called within a redux-form <Field>. Did you decorate it and forget to add the addField prop to your component? See https://marmelab.com/react-admin/Inputs.html#writing-your-own-input-component for details."
            );
        }
        const { open, choice } = this.state;
        const { touched, error } = meta;
        return (
            <Labeled label={this.props.label}>
                <div>
                    <Button raised
                        label={translate('ah.file.button.pick_file')}
                        onTouchTap={this.handleOpen}
                        icon={<IconPhotoLibrary />}
                    />
                    <Dialog
                        actions={
                            <Button
                                label={translate('ah.core.button.cancel')}
                                primary={true}
                                onTouchTap={this.handleClose}
                            />
                        }
                        contentStyle={contentStyle}
                        bodyStyle={bodyStyle}
                        title={translate('ah.file.dialog.picker.title')}
                        onRequestClose={this.handleClose}
                        modal={false}
                        open={open}
                        autoScrollBodyContent={true}
                    >
                        {choices.map(this.renderChoice)}
                    </Dialog>
                    {choice &&
                        displayChoice && (
                            <div style={{ marginTop: '1em' }}>
                                <FileCard
                                    record={choice}
                                    showActions={false}
                                    cardStyle={{ margin: 0 }}
                                    buttonLabel={translate(
                                        'ah.file.button.clear'
                                    )}
                                    handleClick={this.removeValue}
                                />
                            </div>
                        )}
                    <p style={{ color: 'rgb(244, 67, 54)', fontSize: '12px' }}>
                        {touched && error}
                    </p>
                </div>
            </Labeled>
        );
    }
}

DialogInput.propTypes = {
    choices: PropTypes.arrayOf(PropTypes.object),
    input: PropTypes.object,
    translate: PropTypes.func,
    optionValue: PropTypes.string.isRequired,
    meta: PropTypes.object,
    displayChoice: PropTypes.bool.isRequired,
    onFilePick: PropTypes.func,
};

const EnhancedDialogInput = translate(DialogInput);

EnhancedDialogInput.defaultProps = {
    optionValue: '@id',
    displayChoice: true,
    onFilePick: () => {},
};

export default EnhancedDialogInput;
