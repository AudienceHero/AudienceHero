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

use AudienceHero\Bundle\CoreBundle\Entity\Person;
use AudienceHero\Bundle\PodcastBundle\Entity\PodcastChannel;
use AudienceHero\Bundle\PodcastBundle\Entity\PodcastEpisode;
use Symfony\Component\EventDispatcher\Event;

/**
 * PodcastEvent.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
class PodcastEvent extends Event
{
    /** @var PodcastChannel|null */
    private $channel;
    /** @var PodcastEpisode|null */
    private $episode;

    public function __construct(?PodcastChannel $channel, ?PodcastEpisode $episode)
    {
        $this->channel = $channel;
        $this->episode = $episode;
    }

    /**
     * @return PodcastChannel|null
     */
    public function getChannel(): ?PodcastChannel
    {
        return $this->channel;
    }

    /**
     * @return PodcastEpisode|null
     */
    public function getEpisode(): ?PodcastEpisode
    {
        return $this->episode;
    }

    public function getOwner(): ?Person
    {
        if ($this->channel) {
            return $this->channel->getOwner();
        }

        return $this->episode->getOwner();
    }
}
