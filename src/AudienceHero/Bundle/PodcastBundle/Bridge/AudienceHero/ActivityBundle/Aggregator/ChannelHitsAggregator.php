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

namespace AudienceHero\Bundle\PodcastBundle\Bridge\AudienceHero\ActivityBundle\Aggregator;

use AudienceHero\Bundle\ActivityBundle\Aggregator\AbstractAggregator;
use AudienceHero\Bundle\ActivityBundle\Entity\Aggregate;
use AudienceHero\Bundle\PodcastBundle\Entity\PodcastChannel;
use AudienceHero\Bundle\PodcastBundle\Event\PodcastEvents;

class ChannelHitsAggregator extends AbstractAggregator
{
    /**
     * {@inheritdoc}
     */
    public function supportsType(): string
    {
        return PodcastEvents::CHANNEL_HIT;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsClass(): string
    {
        return PodcastChannel::class;
    }

    /**
     * {@inheritdoc}
     */
    public function compute(Aggregate $aggregate): void
    {
        $computer = $this->getAggregateComputer();
        $aggregate->addData(self::AGGREGATE_TOTAL, $computer->countTotal(PodcastChannel::class, $aggregate->getSubjectId(), $aggregate->getType()));
        $aggregate->addData(self::AGGREGATE_DAILY, $computer->countDaily(PodcastChannel::class, $aggregate->getSubjectId(), $aggregate->getType()));
        $aggregate->addData(self::AGGREGATE_TOP10_COUNTRY, $computer->countField(PodcastChannel::class, $aggregate->getSubjectId(), $aggregate->getType(), 'country', 10));
    }
}
