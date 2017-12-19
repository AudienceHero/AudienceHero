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

namespace AudienceHero\Bundle\PodcastBundle\Tests\Event;

use AudienceHero\Bundle\CoreBundle\Entity\Person;
use AudienceHero\Bundle\PodcastBundle\Entity\PodcastChannel;
use AudienceHero\Bundle\PodcastBundle\Entity\PodcastEpisode;
use AudienceHero\Bundle\PodcastBundle\Event\PodcastEvent;
use PHPUnit\Framework\TestCase;

class PodcastEventTest extends TestCase
{
    public function testAccessors()
    {
        $channel = new PodcastChannel();
        $episode = new PodcastEpisode();

        $event = new PodcastEvent($channel, $episode);
        $this->assertSame($channel, $event->getChannel());
        $this->assertSame($episode, $event->getEpisode());
    }

    public function testGetOwnerForChannelOnly()
    {
        $person = $this->prophesize(Person::class);
        $person->getEmail()->willReturn('foobar@example.com');
        $channel = new PodcastChannel();
        $channel->setOwner($person->reveal());

        $event = new PodcastEvent($channel, null);
        $this->assertSame($person->reveal(), $event->getOwner());
    }

    public function testGetOwnerForEpisodeOnly()
    {
        $person = $this->prophesize(Person::class);
        $person->getEmail()->willReturn('foobar@example.com');
        $channel = new PodcastChannel();
        $channel->setOwner($person->reveal());
        $episode = new PodcastEpisode();
        $episode->setChannel($channel);

        $event = new PodcastEvent(null, $episode);
        $this->assertSame($person->reveal(), $event->getOwner());
    }
}
