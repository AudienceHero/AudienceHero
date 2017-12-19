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

namespace AudienceHero\Bundle\PodcastBundle\Action;

use AudienceHero\Bundle\PodcastBundle\Entity\PodcastChannel;
use AudienceHero\Bundle\PodcastBundle\Event\PodcastEvent;
use AudienceHero\Bundle\PodcastBundle\Event\PodcastEvents;
use AudienceHero\Bundle\PodcastBundle\Provider\PodcastChannelEpisodesProvider;
use AudienceHero\Bundle\PodcastBundle\Rss\ChannelBuilder;
use MarcW\RssWriter\Bridge\Symfony\HttpFoundation\RssStreamedResponse;
use MarcW\RssWriter\RssWriter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * FeedAction.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
class FeedAction
{
    /**
     * @var ChannelBuilder
     */
    private $channelBuilder;
    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;
    /**
     * @var RssWriter
     */
    private $rssWriter;
    /**
     * @var PodcastChannelEpisodesProvider
     */
    private $episodesProvider;

    public function __construct(ChannelBuilder $channelBuilder, RssWriter $rssWriter, PodcastChannelEpisodesProvider $episodesProvider, EventDispatcherInterface $eventDispatcher)
    {
        $this->channelBuilder = $channelBuilder;
        $this->eventDispatcher = $eventDispatcher;
        $this->rssWriter = $rssWriter;
        $this->episodesProvider = $episodesProvider;
    }

    /**
     * @Route("/podcasts/{id}.xml", name="podcast_channels_feed", requirements={"slug"="[a-zA-Z-0-9-]+"})
     * @Security("is_granted('FRONT_SEE', channel)")
     */
    public function __invoke(PodcastChannel $channel)
    {
        $this->eventDispatcher->dispatch(PodcastEvents::CHANNEL_FEED_HIT, new PodcastEvent($channel, null));

        $channel = $this->channelBuilder->fromPodcastChannel($channel, $this->episodesProvider->getSeeableEpisodes($channel));

        return new RssStreamedResponse($channel, $this->rssWriter);
    }
}
