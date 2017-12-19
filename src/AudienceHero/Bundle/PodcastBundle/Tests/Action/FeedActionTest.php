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

namespace AudienceHero\Bundle\PodcastBundle\Tests\Action;

use AudienceHero\Bundle\PodcastBundle\Action\FeedAction;
use AudienceHero\Bundle\PodcastBundle\Entity\PodcastChannel;
use AudienceHero\Bundle\PodcastBundle\Event\PodcastEvents;
use AudienceHero\Bundle\PodcastBundle\Provider\PodcastChannelEpisodesProvider;
use AudienceHero\Bundle\PodcastBundle\Rss\ChannelBuilder;
use Doctrine\Common\Collections\ArrayCollection;
use MarcW\RssWriter\Bridge\Symfony\HttpFoundation\RssStreamedResponse;
use MarcW\RssWriter\Extension\Core\Channel;
use MarcW\RssWriter\RssWriter;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class FeedActionTest extends TestCase
{
    /** @var ObjectProphecy */
    private $channelBuilder;
    /** @var ObjectProphecy */
    private $eventDispatcher;
    /** @var ObjectProphecy */
    private $authorizationChecker;
    /** @var ObjectProphecy */
    private $episodesProvider;
    /** @var ObjectProphecy */
    private $rssWriter;

    public function setUp()
    {
        $this->channelBuilder = $this->prophesize(ChannelBuilder::class);
        $this->eventDispatcher = $this->prophesize(EventDispatcherInterface::class);
        $this->episodesProvider = $this->prophesize(PodcastChannelEpisodesProvider::class);
        $this->rssWriter = new RssWriter();
    }

    private function getActionInstance(): FeedAction
    {
        return new FeedAction(
            $this->channelBuilder->reveal(),
            $this->rssWriter,
            $this->episodesProvider->reveal(),
            $this->eventDispatcher->reveal()
        );
    }

    public function testAction()
    {
        $channel = new PodcastChannel();
        $rssChannel = new Channel();

        $collection = new ArrayCollection();
        $this->episodesProvider->getSeeableEpisodes($channel)->shouldBeCalled()->willReturn($collection);

        $this->channelBuilder->fromPodcastChannel($channel, $collection)->shouldBeCalled()->willReturn($rssChannel);

        $this->eventDispatcher->dispatch(PodcastEvents::CHANNEL_FEED_HIT, Argument::that(function ($event) use ($channel) {
            return $channel === $event->getChannel();
        }));

        $action = $this->getActionInstance();
        $response = $action($channel);

        $this->assertInstanceOf(RssStreamedResponse::class, $response);
    }
}
