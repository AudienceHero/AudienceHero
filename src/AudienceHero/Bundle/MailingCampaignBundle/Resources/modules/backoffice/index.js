import { Delete } from 'react-admin';
import {
    MailingCreate,
    MailingEdit,
    MailingList,
    MailingShow,
} from './Mailing';
import sagas from './sagas';
import MailingIcon from 'material-ui-icons/Email';
import messages from './messages';
import StatusField from './StatusField';

export { StatusField };

export const Bundle = {
    messages,
    sagas,
    resources: [
        {
            name: 'mailings',
            list: MailingList,
            create: MailingCreate,
            edit: MailingEdit,
            show: MailingShow,
            remove: Delete,
        },
    ],
    menu: [
        {
            leftIcon: MailingIcon,
            to: '/mailings',
            primaryText: 'ah.mailing.menu.mailings',
        },
    ],
};
