/*
 * Taken from Admin-On-Rest.
 * Licensed under the MIT license.
 *
 * Copyright (c) 2016-present, Francois Zaninotto, Marmelab
 */

export const CHANGE_LOCALE = 'AH/CHANGE_LOCALE';

export const changeLocale = locale => ({
    type: CHANGE_LOCALE,
    payload: locale,
});
