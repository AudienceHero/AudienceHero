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

namespace AudienceHero\Bundle\ActivityBundle\Aggregator;

use AudienceHero\Bundle\ActivityBundle\Entity\Aggregate;

/**
 * AggregatorInterface.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
interface AggregatorInterface
{
    public function supportsType(): string;

    public function supportsClass(): string;

    /**
     * Aggregate data for given subject and given type.
     */
    public function compute(Aggregate $aggregate): void;
}
