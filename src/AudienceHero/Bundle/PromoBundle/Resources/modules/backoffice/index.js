import { PromoCreate, PromoEdit, PromoList } from './Promo';
import { Delete } from 'react-admin';
import PromoIcon from 'material-ui-icons/Hearing';
import sagas from './sagas';
import messages from './messages';
import PromoShow from './PromoShow';

export const Bundle = {
    messages,
    resources: [
        {
            name: 'promos',
            list: PromoList,
            create: PromoCreate,
            edit: PromoEdit,
            show: PromoShow,
            remove: Delete,
        },
    ],
    menu: [
        {
            leftIcon: PromoIcon,
            to: '/promos',
            primaryText: 'ah.promo.menu.promos',
        },
    ],
    sagas: sagas,
};
