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
 * TextStoreChainImporter.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
class TextStoreChainImporter
{
    /** @var TextStoreImporterInterface[] */
    private $importers = [];

    public function addImporter(TextStoreImporterInterface $importer)
    {
        $this->importers[] = $importer;
    }

    public function getImporters(): array
    {
        return $this->importers;
    }

    public function getImporterFor(TextStore $textStore): ?TextStoreImporterInterface
    {
        foreach ($this->importers as $importer) {
            if ($importer->supports($textStore)) {
                return $importer;
            }
        }

        return null;
    }
}
