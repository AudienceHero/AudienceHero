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

namespace AudienceHero\Bundle\PodcastBundle\Tests\Bridge\AudienceHero\ActivityBundle\EventListener;

use AudienceHero\Bundle\ActivityBundle\Builder\ActivityBuilder;
use AudienceHero\Bundle\ActivityBundle\Entity\Activity;
use AudienceHero\Bundle\CoreBundle\Entity\User;
use AudienceHero\Bundle\PodcastBundle\Bridge\AudienceHero\ActivityBundle\EventListener\ActivityEventSubscriber;
use AudienceHero\Bundle\PodcastBundle\Entity\PodcastChannel;
use AudienceHero\Bundle\PodcastBundle\Entity\PodcastEpisode;
use AudienceHero\Bundle\PodcastBundle\Event\PodcastEvent;
use AudienceHero\Bundle\PodcastBundle\Event\PodcastEvents;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;

class ActivityEventSubscriberTest extends TestCase
{
    /** @var ObjectProphecy */
    private $builder;

    public function setUp()
    {
        $this->builder = $this->prophesize(ActivityBuilder::class);
    }

    public function testGetSubscribedEvents()
    {
        $events = ActivityEventSubscriber::getSubscribedEvents();

        $this->assertArrayHasKey(PodcastEvents::CHANNEL_HIT, $events);
        $this->assertArrayHasKey(PodcastEvents::EPISODE_HIT, $events);
        $this->assertArrayHasKey(PodcastEvents::CHANNEL_FEED_HIT, $events);
        $this->assertArrayHasKey(PodcastEvents::EPISODE_ENCLOSURE_DOWNLOAD, $events);

        $subscriber = new ActivityEventSubscriber($this->builder->reveal());
        foreach ($events as $key => $method) {
            $this->assertTrue(method_exists($subscriber, $method));
        }
    }

    public function testOnChannelFeedHit()
    {
        $user = new User();
        $channel = new PodcastChannel();
        $channel->setOwner($user);

        $this->builder->build(
            Argument::type(\DateTime::class),
            $user,
            PodcastEvents::CHANNEL_FEED_HIT,
            $channel
        )->shouldBeCalled();

        $event = new PodcastEvent($channel, null);
        $subscriber = new ActivityEventSubscriber($this->builder->reveal());
        $subscriber->onChannelFeedHit($event);
    }

    public function testOnChannelHit()
    {
        $user = new User();
        $channel = new PodcastChannel();
        $channel->setOwner($user);

        $this->builder->build(
            Argument::type(\DateTime::class),
            $user,
            PodcastEvents::CHANNEL_HIT,
            $channel
        )->shouldBeCalled();

        $event = new PodcastEvent($channel, null);
        $subscriber = new ActivityEventSubscriber($this->builder->reveal());
        $subscriber->onChannelHit($event);
    }

    public function testOnEpisodeEnclosureDownload()
    {
        $user = new User();
        $channel = new PodcastChannel();
        $channel->setOwner($user);
        $episode = new PodcastEpisode();
        $episode->setChannel($channel);

        $activity = new Activity();
        $this->builder->build(
            Argument::type(\DateTime::class),
            $user,
            PodcastEvents::EPISODE_ENCLOSURE_DOWNLOAD,
            $episode
        )->shouldBeCalled()->willReturn($activity);

        $event = new PodcastEvent($channel, $episode);
        $subscriber = new ActivityEventSubscriber($this->builder->reveal());
        $subscriber->onEpisodeEnclosureDownload($event);
        $this->assertSame($channel, $activity->getSubject('podcast_channels'));
    }

    public function testOnEpisodeHit()
    {
        $user = new User();
        $channel = new PodcastChannel();
        $channel->setOwner($user);
        $episode = new PodcastEpisode();
        $episode->setChannel($channel);

        $activity = new Activity();
        $this->builder->build(
            Argument::type(\DateTime::class),
            $user,
            PodcastEvents::EPISODE_HIT,
            $episode
        )->shouldBeCalled()->willReturn($activity);

        $event = new PodcastEvent($channel, $episode);
        $subscriber = new ActivityEventSubscriber($this->builder->reveal());
        $subscriber->onEpisodeHit($event);
        $this->assertSame($channel, $activity->getSubject('podcast_channels'));
    }
}
