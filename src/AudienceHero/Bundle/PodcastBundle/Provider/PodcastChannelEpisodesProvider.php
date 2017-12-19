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

namespace AudienceHero\Bundle\PodcastBundle\Provider;

use AudienceHero\Bundle\PodcastBundle\Entity\PodcastChannel;
use AudienceHero\Bundle\PodcastBundle\Entity\PodcastEpisode;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * PodcastChannelEpisodesProvider.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
class PodcastChannelEpisodesProvider
{
    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    public function __construct(AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->authorizationChecker = $authorizationChecker;
    }

    public function getSeeableEpisodes(PodcastChannel $channel): Collection
    {
        $episodes = $channel->getEpisodes()->filter(function (PodcastEpisode $episode) {
            return $this->authorizationChecker->isGranted('FRONT_SEE', $episode);
        });

        return $episodes;
    }
}
