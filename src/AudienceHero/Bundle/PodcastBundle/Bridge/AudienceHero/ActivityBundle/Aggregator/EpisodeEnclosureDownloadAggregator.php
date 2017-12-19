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
use AudienceHero\Bundle\PodcastBundle\Entity\PodcastEpisode;
use AudienceHero\Bundle\PodcastBundle\Event\PodcastEvents;

/**
 * PodcastEpisodeEnclosureDownloadAggregator.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
class EpisodeEnclosureDownloadAggregator extends AbstractAggregator
{
    public function supportsType(): string
    {
        return PodcastEvents::EPISODE_ENCLOSURE_DOWNLOAD;
    }

    public function supportsClass(): string
    {
        return PodcastEpisode::class;
    }

    /**
     * Aggregate data for given subject and given type.
     */
    public function compute(Aggregate $aggregate): void
    {
        $computer = $this->getAggregateComputer();
        $aggregate->addData(self::AGGREGATE_TOTAL, $computer->countTotal($this->supportsClass(), $aggregate->getSubjectId(), $aggregate->getType()));
        $aggregate->addData(self::AGGREGATE_DAILY, $computer->countDaily($this->supportsClass(), $aggregate->getSubjectId(), $aggregate->getType()));
        $aggregate->addData(self::AGGREGATE_TOP10_COUNTRY, $computer->countField($this->supportsClass(), $aggregate->getSubjectId(), $aggregate->getType(), 'country', 10));
    }
}
