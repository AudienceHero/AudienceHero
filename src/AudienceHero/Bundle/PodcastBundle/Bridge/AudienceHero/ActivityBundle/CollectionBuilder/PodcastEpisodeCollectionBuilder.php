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

namespace AudienceHero\Bundle\PodcastBundle\Bridge\AudienceHero\ActivityBundle\CollectionBuilder;

use AudienceHero\Bundle\ActivityBundle\CollectionBuilder\EntityCollectionBuilderInterface;
use AudienceHero\Bundle\PodcastBundle\Event\PodcastEvents;
use AudienceHero\Bundle\PodcastBundle\Repository\PodcastEpisodeRepository;

/**
 * @author Marc Weistroff <marc@weistroff.net>
 */
class PodcastEpisodeCollectionBuilder implements EntityCollectionBuilderInterface
{
    /**
     * @var PodcastEpisodeRepository
     */
    private $repository;

    public function __construct(PodcastEpisodeRepository $repository)
    {
        $this->repository = $repository;
    }

    public function build(): array
    {
        $entities = [];
        $ids = $this->repository->getAllIds();

        foreach ($ids as $id) {
            $entities[$id['id']] = [
                PodcastEvents::EPISODE_ENCLOSURE_DOWNLOAD,
                PodcastEvents::EPISODE_HIT,
            ];
        }

        return $entities;
    }
}
