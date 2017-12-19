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

namespace AudienceHero\Bundle\PodcastBundle\Tests\Provider;

use AudienceHero\Bundle\PodcastBundle\Entity\PodcastChannel;
use AudienceHero\Bundle\PodcastBundle\Entity\PodcastEpisode;
use AudienceHero\Bundle\PodcastBundle\Provider\PodcastChannelEpisodesProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class PodcastChannelEpisodesProviderTest extends TestCase
{
    public function testGetSeeableEpisodes()
    {
        $channel = new PodcastChannel();
        $ep1 = new PodcastEpisode();
        $ep1->setTitle('foo');
        $ep2 = new PodcastEpisode();
        $ep2->setTitle('bar');
        $ep3 = new PodcastEpisode();
        $ep3->setTitle('baz');

        $channel->addEpisode($ep1);
        $channel->addEpisode($ep2);
        $channel->addEpisode($ep3);

        $authorizationChecker = $this->prophesize(AuthorizationCheckerInterface::class);
        $authorizationChecker->isGranted('FRONT_SEE', $ep1)->shouldBeCalled()->willReturn(true);
        $authorizationChecker->isGranted('FRONT_SEE', $ep2)->shouldBeCalled()->willReturn(false);
        $authorizationChecker->isGranted('FRONT_SEE', $ep3)->shouldBeCalled()->willReturn(true);

        $provider = new PodcastChannelEpisodesProvider($authorizationChecker->reveal());
        $collection = $provider->getSeeableEpisodes($channel);
        $this->assertCount(2, $collection);
        $this->assertSame($ep1, $collection->first());
        $this->assertSame($ep3, $collection->last());
    }
}
