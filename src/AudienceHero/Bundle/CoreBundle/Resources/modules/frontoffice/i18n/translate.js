/*
 * Taken from Admin-On-Rest.
 * Licensed under the MIT license.
 *
 * Copyright (c) 2016-present, Francois Zaninotto, Marmelab
 */
import PropTypes from 'prop-types';
import { getContext } from 'recompose';

const translate = BaseComponent => {
    const TranslatedComponent = getContext({
        translate: PropTypes.func.isRequired,
        locale: PropTypes.string.isRequired,
    })(BaseComponent);

    TranslatedComponent.defaultProps = BaseComponent.defaultProps;

    return TranslatedComponent;
};

export default translate;
