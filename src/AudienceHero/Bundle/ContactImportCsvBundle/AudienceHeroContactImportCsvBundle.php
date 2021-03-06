<?php

/*
 * This file is part of the AudienceHero project.
 *
 * (c) Marc Weistroff <marc@weistroff.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace AudienceHero\Bundle\ContactImportCsvBundle;

use AudienceHero\Bundle\CoreBundle\Behavior\Module\ModuleProviderInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * AudienceHeroContactImportCSVBundle.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
class AudienceHeroContactImportCsvBundle extends Bundle implements ModuleProviderInterface
{
    /**
     * Returns the name of the javascript module to register for the Backoffice application.
     * Return null in case you don't have any back office module to register.
     */
    public function getBackOfficeModule(): ?string
    {
        return '@audiencehero-backoffice/contact-import-csv';
    }

    /**
     * Returns the name of the javascript module to register for the Backoffice application.
     * Return null in case you don't have any front office module to register.
     */
    public function getFrontOfficeModule(): ?string
    {
        return null;
    }
}
