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

/**
 * EnricherInterface describes an Enricher. An Enricher is an object that, from existing data,
 * can derive other data and store then in the Activity class.
 */
interface EnricherInterface
{
    /**
     * @param Activity $activity activity to enrich
     */
    public function enrich(Activity $activity): void;
}
