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

namespace AudienceHero\Bundle\AcquisitionFreeDownloadBundle\Bridge\AudienceHero\ActivityBundle\EventListener;

use AudienceHero\Bundle\AcquisitionFreeDownloadBundle\Events\AcquisitionFreeDownloadEvent;
use AudienceHero\Bundle\AcquisitionFreeDownloadBundle\Events\AcquisitionFreeDownloadEvents;
use AudienceHero\Bundle\ActivityBundle\Builder\ActivityBuilder;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * ActivityEventSubscriber.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
class ActivityEventSubscriber implements EventSubscriberInterface
{
    private $builder;

    public function __construct(ActivityBuilder $builder)
    {
        $this->builder = $builder;
    }

    public static function getSubscribedEvents()
    {
        return [
            AcquisitionFreeDownloadEvents::HIT => 'onHit',
            AcquisitionFreeDownloadEvents::UNLOCK => 'onUnlock',
        ];
    }

    public function onHit(AcquisitionFreeDownloadEvent $event): void
    {
        $acquisitionFreeDownload = $event->getAcquisitionFreeDownload();
        $this->builder->build(new \DateTime(), $acquisitionFreeDownload->getOwner(), AcquisitionFreeDownloadEvents::HIT, $acquisitionFreeDownload);
    }

    public function onUnlock(AcquisitionFreeDownloadEvent $event): void
    {
        $acquisitionFreeDownload = $event->getAcquisitionFreeDownload();
        $activity = $this->builder->build(new \DateTime(), $acquisitionFreeDownload->getOwner(), AcquisitionFreeDownloadEvents::UNLOCK, $acquisitionFreeDownload);
        $activity->addSubject($event->getContactsGroupContact()->getContact());
    }
}
