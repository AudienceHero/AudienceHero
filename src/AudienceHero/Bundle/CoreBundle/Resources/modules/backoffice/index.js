export * from './Auth';
export * from './Dashboard';
export * from './detail';
export * from './field';
export * from './Import';
export * from './input';
export * from './list';
export * from './utils';

import { Delete } from 'react-admin';
import { PersonEmailCreate, PersonEmailList } from './Resources/PersonEmail';
import sagas from './sagas';
import routes from './routes';
import PersonEmailIcon from 'material-ui-icons/Email';
import TagIcon from 'material-ui-icons/LabelOutline';
import messages from './messages';
import { TagCreate, TagEdit, TagList } from './Resources/Tag';
import Image from './Image';
import Layout from './Layout';

export { Image, Layout };

import reducer from './reducer';

export const Bundle = {
    messages,
    sagas,
    routes,
    reducer: {
        ah_core: reducer,
    },
    resources: [
        {
            name: 'person_emails',
            list: PersonEmailList,
            create: PersonEmailCreate,
            remove: Delete,
        },
        {
            name: 'tags',
            list: TagList,
            create: TagCreate,
            edit: TagEdit,
            remove: Delete,
        },
        { name: 'text_stores' },
    ],
    menu: [
        {
            primaryText: 'ah.core.menu.tags',
            leftIcon: TagIcon,
            to: '/tags',
        },
    ],
    settingsMenu: {
        primaryText: 'ah.core.menu.emails',
        leftIcon: PersonEmailIcon,
        to: '/person_emails',
    },
};
