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

namespace AudienceHero\Bundle\PodcastBundle\Event;

/**
 * PodcastEvents.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
final class PodcastEvents
{
    const CHANNEL_HIT = 'podcast_channel.hit';
    const CHANNEL_FEED_HIT = 'podcast_channel.feed_hit';

    const EPISODE_HIT = 'podcast_episode.hit';
    const EPISODE_ENCLOSURE_DOWNLOAD = 'podcast_episode.enclosure_download';
}
