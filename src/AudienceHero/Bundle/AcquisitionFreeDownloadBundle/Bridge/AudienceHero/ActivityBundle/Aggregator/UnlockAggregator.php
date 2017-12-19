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

namespace AudienceHero\Bundle\AcquisitionFreeDownloadBundle\Bridge\AudienceHero\ActivityBundle\Aggregator;

use AudienceHero\Bundle\AcquisitionFreeDownloadBundle\Entity\AcquisitionFreeDownload;
use AudienceHero\Bundle\AcquisitionFreeDownloadBundle\Events\AcquisitionFreeDownloadEvents;
use AudienceHero\Bundle\ActivityBundle\Aggregator\AbstractAggregator;
use AudienceHero\Bundle\ActivityBundle\Entity\Aggregate;

class UnlockAggregator extends AbstractAggregator
{
    /**
     * {@inheritdoc}
     */
    public function supportsType(): string
    {
        return AcquisitionFreeDownloadEvents::UNLOCK;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsClass(): string
    {
        return AcquisitionFreeDownload::class;
    }

    /**
     * {@inheritdoc}
     */
    public function compute(Aggregate $aggregate): void
    {
        $aggregate->addData(self::AGGREGATE_TOTAL, $this->getAggregateComputer()->countTotal(AcquisitionFreeDownload::class, $aggregate->getSubjectId(), $aggregate->getType()));
        $aggregate->addData(self::AGGREGATE_DAILY, $this->getAggregateComputer()->countDaily(AcquisitionFreeDownload::class, $aggregate->getSubjectId(), $aggregate->getType()));
    }
}
