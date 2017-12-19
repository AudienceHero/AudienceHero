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

namespace AudienceHero\Bundle\MailingCampaignBundle\Bridge\AudienceHero\ActivityBundle\Aggregator;

use AudienceHero\Bundle\ActivityBundle\Aggregator\AbstractAggregator;
use AudienceHero\Bundle\ActivityBundle\Entity\Aggregate;
use AudienceHero\Bundle\MailingCampaignBundle\Entity\EmailEvent;
use AudienceHero\Bundle\MailingCampaignBundle\Entity\Mailing;

/**
 * SendAggregator.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
class SendAggregator extends AbstractAggregator
{
    public function supportsType(): string
    {
        return EmailEvent::EVENT_SEND;
    }

    public function supportsClass(): string
    {
        return Mailing::class;
    }

    public function compute(Aggregate $aggregate): void
    {
        $aggregate->addData(self::AGGREGATE_TOTAL, $this->getAggregateComputer()->countTotal($this->supportsClass(), $aggregate->getSubjectId(), $this->supportsType()));
    }
}
