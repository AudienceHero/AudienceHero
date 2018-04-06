import { flattenBundleProperty, flattenReducers } from '@audiencehero/common';
import { Bundle as AudienceHeroAcquisitionFreeDownloadBundle } from '@audiencehero-backoffice/acquisition-free-download';
import { Bundle as AudienceHeroActivityBundle } from '@audiencehero-backoffice/activity';
import { Bundle as AudienceHeroContactBundle } from '@audiencehero-backoffice/contact';
import { Bundle as AudienceHeroContactImportCsvBundle } from '@audiencehero-backoffice/contact-import-csv';
import { Bundle as AudienceHeroCoreBundle } from '@audiencehero-backoffice/core';
import { Bundle as AudienceHeroFileBundle } from '@audiencehero-backoffice/file';
import { Bundle as AudienceHeroMailingCampaignBundle } from '@audiencehero-backoffice/mailing-campaign';
import { Bundle as AudienceHeroPodcastBundle } from '@audiencehero-backoffice/podcast';
import { Bundle as AudienceHeroPromoBundle } from '@audiencehero-backoffice/promo';
const bundles = [
    AudienceHeroAcquisitionFreeDownloadBundle,
    AudienceHeroActivityBundle,
    AudienceHeroContactBundle,
    AudienceHeroContactImportCsvBundle,
    AudienceHeroCoreBundle,
    AudienceHeroFileBundle,
    AudienceHeroMailingCampaignBundle,
    AudienceHeroPodcastBundle,
    AudienceHeroPromoBundle,
];
export const customReducers = flattenReducers(bundles);
export const resources = flattenBundleProperty(bundles, 'resources');
export const menu = flattenBundleProperty(bundles, 'menu');
export const customRoutes = flattenBundleProperty(bundles, 'routes');
export const customSagas = flattenBundleProperty(bundles, 'sagas');
export const settingsMenu = flattenBundleProperty(bundles, 'settingsMenu');
export const importMenu = flattenBundleProperty(bundles, 'importMenu');
export const bundleMessages = flattenBundleProperty(bundles, 'messages');
