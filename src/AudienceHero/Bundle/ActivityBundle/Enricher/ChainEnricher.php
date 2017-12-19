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

namespace AudienceHero\Bundle\ActivityBundle\Enricher;

use AudienceHero\Bundle\ActivityBundle\Entity\Activity;

class ChainEnricher
{
    /** @var EnricherInterface[] */
    private $enrichers = [];

    public function addEnricher(EnricherInterface $enricher): void
    {
        if ($enricher === $this) {
            return;
        }

        $this->enrichers[] = $enricher;
    }

    public function enrich(Activity $activity): void
    {
        foreach ($this->enrichers as $enricher) {
            $enricher->enrich($activity);
        }
    }
}
