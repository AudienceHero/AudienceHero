import { combineReducers } from 'redux';
import loading from './loading';
import notification from './notification';
import data from './data';
import title from './title';
import i18n from './i18n';

export default combineReducers({
    data,
    loading,
    notification,
    title,
    i18n,
});
