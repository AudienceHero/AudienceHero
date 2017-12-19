/* Taken from Admin-On-Rest.
 * Licensed under the MIT license.
 *
 * Copyright (c) 2016-present, Francois Zaninotto, Marmelab
 */
import { DEFAULT_LOCALE } from '../i18n/index';
import { CHANGE_LOCALE } from '../actions/localeActions';

export default (initialLocale = DEFAULT_LOCALE) => (
    previousLocale = initialLocale,
    { type, payload }
) => {
    switch (type) {
        case CHANGE_LOCALE:
            return payload;
        default:
            return previousLocale;
    }
};
