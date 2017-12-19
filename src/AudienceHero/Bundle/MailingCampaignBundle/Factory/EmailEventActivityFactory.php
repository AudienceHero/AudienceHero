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

namespace AudienceHero\Bundle\MailingCampaignBundle\Factory;

use AudienceHero\Bundle\ActivityBundle\Entity\Activity;
use AudienceHero\Bundle\MailingCampaignBundle\Entity\EmailEvent;

/**
 * EmailEventActivityFactory.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
class EmailEventActivityFactory
{
    public function createFromEmailEvent(EmailEvent $emailEvent): Activity
    {
        $mailingRecipient = $emailEvent->getEmail()->getMailingRecipient();
        if (!$mailingRecipient) {
            throw new \InvalidArgumentException(sprintf('No mailing recipient in email %s', $emailEvent->getEmail()->getId()));
        }

        $activity = new Activity();
        $activity->setOwner($mailingRecipient->getOwner());
        $activity->addSubject($mailingRecipient);
        $activity->addSubject($mailingRecipient->getMailing());
        $activity->setType($emailEvent->getEvent());

        return $activity;
    }
}
