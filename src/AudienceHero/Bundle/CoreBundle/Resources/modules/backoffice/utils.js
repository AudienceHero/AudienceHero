import cyan from 'material-ui/colors/cyan';
import pink from 'material-ui/colors/pink';
const cyan500 = cyan['500'];
const pinkA200 = pink.A200;
import React from 'react';
import TextField from 'material-ui/TextField';

export const styles = {
    main: {
        display: 'flex',
        flexDirection: 'column',
        minHeight: '100vh',
        alignItems: 'center',
        justifyContent: 'center',
    },
    card: {
        minWidth: 300,
    },
    avatar: {
        margin: '1em',
        textAlign: 'center ',
    },
    form: {
        padding: '0 1em 1em 1em',
    },
    input: {
        display: 'flex',
    },
};

export const getColorsFromTheme = theme => {
    if (!theme) return { primary1Color: cyan500, accent1Color: pinkA200 };
    const { palette: { primary1Color, accent1Color } } = theme;
    return { primary1Color, accent1Color };
};

// see http://redux-form.com/6.4.3/examples/material-ui/
export const renderInput = ({
    meta: { touched, error } = {},
    input: { ...inputProps },
    ...props
}) => (
    <TextField
        errorText={touched && error}
        {...inputProps}
        {...props}
        fullWidth
    />
);
