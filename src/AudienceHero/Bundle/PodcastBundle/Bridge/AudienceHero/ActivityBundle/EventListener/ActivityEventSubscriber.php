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

namespace AudienceHero\Bundle\PodcastBundle\Bridge\AudienceHero\ActivityBundle\EventListener;

use AudienceHero\Bundle\ActivityBundle\Builder\ActivityBuilder;
use AudienceHero\Bundle\PodcastBundle\Event\PodcastEvent;
use AudienceHero\Bundle\PodcastBundle\Event\PodcastEvents;
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
            PodcastEvents::CHANNEL_HIT => 'onChannelHit',
            PodcastEvents::EPISODE_HIT => 'onEpisodeHit',
            PodcastEvents::CHANNEL_FEED_HIT => 'onChannelFeedHit',
            PodcastEvents::EPISODE_ENCLOSURE_DOWNLOAD => 'onEpisodeEnclosureDownload',
        ];
    }

    public function onChannelFeedHit(PodcastEvent $event)
    {
        $this->builder->build(new \DateTime(), $event->getOwner(), PodcastEvents::CHANNEL_FEED_HIT, $event->getChannel());
    }

    public function onChannelHit(PodcastEvent $event)
    {
        $this->builder->build(new \DateTime(), $event->getOwner(), PodcastEvents::CHANNEL_HIT, $event->getChannel());
    }

    public function onEpisodeEnclosureDownload(PodcastEvent $event)
    {
        $activity = $this->builder->build(new \DateTime(), $event->getOwner(), PodcastEvents::EPISODE_ENCLOSURE_DOWNLOAD, $event->getEpisode());
        $activity->addSubject($event->getChannel());
    }

    public function onEpisodeHit(PodcastEvent $event)
    {
        $activity = $this->builder->build(new \DateTime(), $event->getOwner(), PodcastEvents::EPISODE_HIT, $event->getEpisode());
        $activity->addSubject($event->getChannel());
    }
}
