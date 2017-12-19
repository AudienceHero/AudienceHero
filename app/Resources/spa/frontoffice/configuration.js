import { flattenBundleProperty, flattenReducers } from '@audiencehero/common';
import { Bundle as AudienceHeroAcquisitionFreeDownloadBundle } from '@audiencehero-frontoffice/acquisition-free-download';
import { Bundle as AudienceHeroActivityBundle } from '@audiencehero-frontoffice/activity';
import { Bundle as AudienceHeroContactBundle } from '@audiencehero-frontoffice/contact';
import { Bundle as AudienceHeroCoreBundle } from '@audiencehero-frontoffice/core';
import { Bundle as AudienceHeroFileBundle } from '@audiencehero-frontoffice/file';
import { Bundle as AudienceHeroPodcastBundle } from '@audiencehero-frontoffice/podcast';
import { Bundle as AudienceHeroPromoBundle } from '@audiencehero-frontoffice/promo';

const bundles = [
    AudienceHeroAcquisitionFreeDownloadBundle,
    AudienceHeroActivityBundle,
    AudienceHeroContactBundle,
    AudienceHeroCoreBundle,
    AudienceHeroFileBundle,
    AudienceHeroPodcastBundle,
    AudienceHeroPromoBundle,
];

export const reducers = flattenReducers(bundles);
export const routes = flattenBundleProperty(bundles, 'routes');
export const sagas = flattenBundleProperty(bundles, 'sagas');
export const bundleMessages = flattenBundleProperty(bundles, 'messages');
