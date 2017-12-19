import React from 'react';
import TextField from 'material-ui/TextField';
import translate from '../i18n/translate';

export class TextInput extends React.Component {
    render() {
        const {
            label,
            helperText,
            translate,
            input: { value, onChange },
            meta: { touched, error },
        } = this.props;
        console.log('input', label, touched, error);

        return (
            <TextField
                label={translate(label)}
                helperText={touched && error ? error : translate(helperText)}
                fullWidth
                margin="normal"
                value={value}
                onChange={onChange}
                error={error}
            />
        );
    }
}

export default translate(TextInput);
