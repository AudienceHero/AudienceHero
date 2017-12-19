import FileIcon from 'material-ui-icons/Folder';
import IconArrowDropRight from 'material-ui-icons/ArrowDropDown';
import sagas from './sagas';
import { Delete } from 'react-admin';
import { FileList, FileEdit, FileShow } from './File';
import PlayerIcon from 'material-ui-icons/PlaylistPlay';
import { PlayerCreate, PlayerEdit, PlayerList } from './Player';
import messages from './messages';
import reducer from './reducer';

import DialogInput from './DialogInput';

export { DialogInput };

export const Bundle = {
    sagas,
    messages,
    reducer: {
        ah_file: reducer,
    },
    resources: [
        {
            name: 'players',
            list: PlayerList,
            create: PlayerCreate,
            edit: PlayerEdit,
            remove: Delete,
        },
        { name: 'player_tracks' },
        {
            name: 'files',
            list: FileList,
            show: FileShow,
            edit: FileEdit,
            remove: Delete,
        },
    ],
    menu: [
        {
            primaryText: 'ah.file.menu.root',
            rightIcon: IconArrowDropRight,
            leftIcon: FileIcon,
            menuItems: [
                {
                    leftIcon: FileIcon,
                    to: '/files',
                    primaryText: 'ah.file.menu.files',
                },
                {
                    leftIcon: FileIcon,
                    to: '/files/video-maker',
                    primaryText: 'ah.file.menu.video_maker',
                },
                {
                    leftIcon: PlayerIcon,
                    to: '/players',
                    primaryText: 'ah.file.menu.players',
                },
            ],
        },
    ],
};
