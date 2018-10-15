# AudienceHero

[![GitHub license](https://img.shields.io/github/license/audiencehero/audiencehero.svg)](https://github.com/AudienceHero/AudienceHero/blob/master/LICENSE)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/AudienceHero/AudienceHero/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/AudienceHero/AudienceHero/?branch=master)

AudienceHero is a modular, API-Centric, and multi-tenant community Management System for the independent creators. 
It aims to be the all-in-one toolkit for anybody needing to sustainably grow an audience. 

The project is organized around a core, with extensions providing several different features.

- AcquisitionFreeDownloadBundle: Exchange media against fans email addresses.
- ActivityBundle: Log events, and aggregate stats.
- ContactBundle: Contact Management.
- ContactImportCsvBundle: Import contacts details from a CSV file.
- CoreBundle: AudienceHero's core.
- FileBundle: Media management.
- ImageServerBundle: Image server.
- MailingCampaign: Send mass mailing to contacts.
- PodcastBundle: Podcast channels and episodes management.
- PromoBundle: Send unreleased media to people, and gather feedback.
- SitemapBundle: Generate sitemaps.


## Warning

AudienceHero is **alpha software**. Things can be broken, and might change. 

## Installation

1. `composer install`
2. `yarn`
3. `./bin/console audiencehero:generate:configuration`
4. `NODE_ENV=development yarn run encore dev`

Experimental:

1. docker-compose up

At this point, you have all the necessary files to run the projet. You will still need to configure your virtualhost.

## Documentation

Documentation is available in the `docs` directory, and on the official website: http://www.audiencehero.org.

## Contributing

Contributions are welcome. There is plenty of ways to make this project better, and code is only a small part of then.
You can contribute by:

- writing documentation
- fixing typos
- tweaking the design
- tweaking the UX
- contributing to the core
- writing extensions
- sponsoring the development

Before engaging in a large contribution, be sure to communicate with the project team before.

The project is subject to a [Contributor Code Of Conduct](/conduct.md).

## About

AudienceHero has been created by [Marc Weistroff](https://marc.weistroff.net).

## Licensing

AudienceHero is an Open Source software, released under the MIT License. 
