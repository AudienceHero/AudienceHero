export * from './input';

import { Delete } from 'react-admin';
import * as C from './Contact';
import * as CG from './ContactsGroup';
import * as CGF from './ContactsGroupForm';
import IconArrowDropRight from 'material-ui-icons/ArrowDropDown';
import ContactIcon from 'material-ui-icons/Person';
import ContactsGroupIcon from 'material-ui-icons/Group';
import ContactsGroupFormIcon from 'material-ui-icons/Input';
import messages from './messages';

const Bundle = {
    messages,
    resources: [
        {
            name: 'contacts',
            list: C.ContactList,
            create: C.ContactCreate,
            edit: C.ContactEdit,
            remove: Delete,
        },
        {
            name: 'contacts_groups',
            list: CG.ContactsGroupList,
            create: CG.ContactsGroupCreate,
            edit: CG.ContactsGroupEdit,
            remove: Delete,
        },
        { name: 'contacts_group_contacts' },
        {
            name: 'contacts_group_forms',
            list: CGF.ContactsGroupFormList,
            create: CGF.ContactsGroupFormCreate,
            edit: CGF.ContactsGroupFormEdit,
            show: CGF.ContactsGroupFormShow,
            remove: Delete,
        },
    ],
    menu: [
        {
            primaryText: 'ah.contact.menu.root',
            rightIcon: IconArrowDropRight,
            leftIcon: ContactsGroupIcon,
            menuItems: [
                {
                    leftIcon: ContactIcon,
                    to: '/contacts',
                    primaryText: 'ah.contact.menu.contacts',
                },
                {
                    leftIcon: ContactsGroupIcon,
                    to: '/contacts_groups',
                    primaryText: 'ah.contact.menu.groups',
                },
                {
                    leftIcon: ContactsGroupFormIcon,
                    to: '/contacts_group_forms',
                    primaryText: 'ah.contact.menu.forms',
                },
            ],
        },
    ],
};

export { Bundle };
