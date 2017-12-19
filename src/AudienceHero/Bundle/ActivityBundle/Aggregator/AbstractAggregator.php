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

/**
 * AbstractAggregator.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
abstract class AbstractAggregator implements AggregatorInterface
{
    const AGGREGATE_TOTAL = 'total';
    const AGGREGATE_DAILY = 'daily';
    const AGGREGATE_TOP10_COUNTRY = 'top10_countries';

    /**
     * @var AggregateComputer
     */
    private $aggregateComputer;

    public function __construct(AggregateComputer $aggregateComputer)
    {
        $this->aggregateComputer = $aggregateComputer;
    }

    /**
     * @return AggregateComputer
     */
    public function getAggregateComputer(): AggregateComputer
    {
        return $this->aggregateComputer;
    }
}
