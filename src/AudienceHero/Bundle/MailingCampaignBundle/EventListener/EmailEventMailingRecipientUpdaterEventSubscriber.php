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

namespace AudienceHero\Bundle\MailingCampaignBundle\EventListener;

use AudienceHero\Bundle\ContactBundle\Manager\OptManager;
use AudienceHero\Bundle\MailingCampaignBundle\Entity\EmailEvent;
use AudienceHero\Bundle\MailingCampaignBundle\Entity\MailingRecipient;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;

/**
 * EmailEventMailingRecipientUpdaterEventSubscriber.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
class EmailEventMailingRecipientUpdaterEventSubscriber implements EventSubscriber
{
    /**
     * @var OptManager
     */
    private $optManager;

    public function __construct(OptManager $optManager)
    {
        $this->optManager = $optManager;
    }

    /**
     * Returns an array of events this subscriber wants to listen to.
     *
     * @return array
     */
    public function getSubscribedEvents()
    {
        return ['postPersist'];
    }

    public function postPersist(LifecycleEventArgs $eventArgs)
    {
        $emailEvent = $eventArgs->getEntity();
        if (!$emailEvent instanceof EmailEvent) {
            return;
        }

        $mailingRecipient = $emailEvent->getEmail()->getMailingRecipient();
        if ($emailEvent->isSend()) {
            $mailingRecipient->setStatus(MailingRecipient::STATUS_SENT);
        } elseif ($emailEvent->isOpen()) {
            $mailingRecipient->incrementMailOpenCounter();
            $mailingRecipient->setStatus(MailingRecipient::STATUS_OPENED);
        } elseif ($emailEvent->isClick()) {
            $mailingRecipient->incrementMailClickCounter();
            $mailingRecipient->setStatus(MailingRecipient::STATUS_OPENED);
        } elseif ($emailEvent->isBounce()) {
            $mailingRecipient->setStatus(MailingRecipient::STATUS_NOT_DELIVERED);

            if ($emailEvent->isHardBounce()) {
                $this->optManager->cleanForHardBounce($mailingRecipient->getContactsGroupContact());
            }
        } elseif ($emailEvent->isReject()) {
            $mailingRecipient->setStatus(MailingRecipient::STATUS_NOT_DELIVERED);
        } elseif ($emailEvent->isSpam()) {
            $this->optManager->cleanForSpam($mailingRecipient->getContactsGroupContact());
            $mailingRecipient->setStatus(MailingRecipient::STATUS_SENT);
        }

        $eventArgs->getEntityManager()->flush();
    }
}
