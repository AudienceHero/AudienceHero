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

namespace AudienceHero\Bundle\ActivityBundle\EventListener;

use AudienceHero\Bundle\ActivityBundle\Event\ActivityEvent;
use AudienceHero\Bundle\ActivityBundle\Event\ActivityEvents;
use AudienceHero\Bundle\ActivityBundle\Queue\ActivityProducer;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * AggregateEventSubscriber.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
class AggregateEventSubscriber implements EventSubscriberInterface
{
    /** @var ActivityProducer */
    private $producer;

    public function __construct(ActivityProducer $producer)
    {
        $this->producer = $producer;
    }

    public static function getSubscribedEvents()
    {
        return [
            ActivityEvents::POST_ENRICH => 'onActivityPostEnrich',
        ];
    }

    public function onActivityPostEnrich(ActivityEvent $event)
    {
        $this->producer->aggregate($event->getActivity());
    }
}
