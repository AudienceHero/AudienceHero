import { Delete } from 'react-admin';
import {
    AcquisitionFreeDownloadList,
    AcquisitionFreeDownloadCreate,
    AcquisitionFreeDownloadEdit,
} from './AcquisitionFreeDownload';
import AcquisitionFreeDownloadIcon from 'material-ui-icons/Loop';
import AcquisitionFreeDownloadShow from './AcquisitionFreeDownloadShow';
import messages from './messages';

const Bundle = {
    resources: [
        {
            name: 'acquisition_free_downloads',
            list: AcquisitionFreeDownloadList,
            show: AcquisitionFreeDownloadShow,
            create: AcquisitionFreeDownloadCreate,
            edit: AcquisitionFreeDownloadEdit,
            remove: Delete,
        },
    ],
    messages,
    menu: [
        {
            leftIcon: AcquisitionFreeDownloadIcon,
            to: '/acquisition_free_downloads',
            primaryText: 'ah.afd.menu.free_downloads',
        },
    ],
};

export { Bundle };
