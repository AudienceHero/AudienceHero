<?php

/*
 * This file is part of the AudienceHero project.
 *
 * (c) Marc Weistroff <marc@weistroff.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AudienceHero\Bundle\MailingCampaignBundle\Factory;
use AudienceHero\Bundle\MailingCampaignBundle\Entity\Mailing;


/**
 * MailingFactory
 * @author Marc Weistroff <marc@weistroff.net>
 */
class MailingFactory
{
    public function createBoost(Mailing $mailing): void
    {
        $boost = clone $mailing;

        $boost->setReference(sprintf('Boost %s', $boost->getReference()));
        $boost->setStatus(Mailing::STATUS_DRAFT);
        $boost->setIsInternal(true);
        $boost->setRecipients([]);
        $boost->setSubject(sprintf('Re: %s', $boost->getSubject()));

        $mailing->setBoostMailing($boost);
    }
}