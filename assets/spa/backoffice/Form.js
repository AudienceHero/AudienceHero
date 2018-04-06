import React from 'react';
import PropTypes from 'prop-types';
import { connect } from 'react-redux';
import compose from 'recompose/compose';
import { reduxForm } from 'redux-form';
import { FormField } from 'react-admin';

const formStyle = { padding: '0 1em 1em 1em' };

class Form extends React.Component {
    handleSubmitWithRedirect = (redirect = this.props.redirect) =>
        this.props.handleSubmit(values => this.props.save(values, redirect));
    render() {
        const { children, toolbar, invalid, submitOnEnter } = this.props;
        return (
            <form className="simple-form audiencehero-form">
                <div style={formStyle}>
                    {React.Children.map(
                        children,
                        input =>
                            input && (
                                <div
                                    key={input.props.source}
                                    className={`aor-input-${input.props
                                        .source}`}
                                    style={input.props.style}
                                >
                                    <FormField
                                        name={input.props.source}
                                        input={input}
                                        resource=""
                                        record={{}}
                                        basePath={''}
                                    />
                                </div>
                            )
                    )}
                </div>
                {toolbar &&
                    React.cloneElement(toolbar, {
                        handleSubmitWithRedirect: this.handleSubmitWithRedirect,
                        invalid,
                        submitOnEnter,
                    })}
            </form>
        );
    }
}

Form.propTypes = {
    children: PropTypes.node,
    toolbar: PropTypes.element,
    handleSubmit: PropTypes.func, // passed by redux-form
    save: PropTypes.func, // The thing that triggers the REST submission
    invalid: PropTypes.bool,
    submitOnEnter: PropTypes.bool,
};

Form.defaultProps = {
    invalid: false,
    submitOnEnter: true,
};

const enhance = compose(
    connect((state, props) => ({})),
    reduxForm({
        form: 'form',
        enableReinitialize: true,
    })
);

export default enhance(Form);
