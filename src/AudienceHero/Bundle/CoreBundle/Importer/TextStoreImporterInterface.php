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

namespace AudienceHero\Bundle\CoreBundle\Importer;

use AudienceHero\Bundle\CoreBundle\Entity\TextStore;

/**
 * TextStoreImporterInterface.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
interface TextStoreImporterInterface
{
    public function supports(TextStore $textStore): bool;
    public function import(TextStore $textStore): void;
}
