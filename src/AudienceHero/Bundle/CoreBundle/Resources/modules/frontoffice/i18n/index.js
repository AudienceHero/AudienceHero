/* Taken from Admin-On-Rest.
 * Licensed under the MIT license.
 *
 * Copyright (c) 2016-present, Francois Zaninotto, Marmelab
 */

export const DEFAULT_LOCALE = 'en';

import TranslationProvider from './TranslationProvider';
import translate from './translate';
export * from './TranslationUtils';
export * from './messages';
export { translate, TranslationProvider };
