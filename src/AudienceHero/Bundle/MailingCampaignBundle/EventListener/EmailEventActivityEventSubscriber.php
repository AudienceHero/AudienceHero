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

use AudienceHero\Bundle\MailingCampaignBundle\Entity\EmailEvent;
use AudienceHero\Bundle\MailingCampaignBundle\Factory\EmailEventActivityFactory;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;

/**
 * EmailEventEventSubscriber.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
class EmailEventActivityEventSubscriber implements EventSubscriber
{
    /**
     * @var EmailEventActivityFactory
     */
    private $activityFactory;

    public function __construct(EmailEventActivityFactory $activityFactory)
    {
        $this->activityFactory = $activityFactory;
    }

    public function getSubscribedEvents()
    {
        return [
            'postPersist',
        ];
    }

    /**
     * postPersist creates an Activity based on the EmailEvent content.
     *
     * @param LifecycleEventArgs $eventArgs
     */
    public function postPersist(LifecycleEventArgs $eventArgs)
    {
        $entity = $eventArgs->getEntity();

        if (!$entity instanceof EmailEvent) {
            return;
        }

        $activity = $this->activityFactory->createFromEmailEvent($entity);
        $em = $eventArgs->getEntityManager();
        $em->persist($activity);
        $em->flush();
    }
}
