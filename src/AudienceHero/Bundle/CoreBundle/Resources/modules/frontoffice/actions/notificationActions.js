// Taken from Admin-On-Rest.
// Licensed under the MIT licensed.
//
// Copyright (c) 2016-present, Francois Zaninotto, Marmelab

export const SHOW_NOTIFICATION = 'AH/SHOW_NOTIFICATION';

export const showNotification = (text, type = 'info') => ({
    type: SHOW_NOTIFICATION,
    payload: { text, type },
});

export const HIDE_NOTIFICATION = 'AH/HIDE_NOTIFICATION';

export const hideNotification = () => ({
    type: HIDE_NOTIFICATION,
});
