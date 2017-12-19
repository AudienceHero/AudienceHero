import { addImporter } from '@audiencehero-backoffice/core';
import CsvImportCard from './CsvImportCard';
import messages from './messages';
import sagas from './sagas';
import routes from './routes';

addImporter(CsvImportCard);

export const Bundle = {
    messages,
    sagas,
    routes,
};
