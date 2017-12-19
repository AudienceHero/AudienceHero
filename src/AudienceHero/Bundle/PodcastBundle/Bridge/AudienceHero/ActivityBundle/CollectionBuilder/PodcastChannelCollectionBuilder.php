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

use AudienceHero\Bundle\AcquisitionFreeDownloadBundle\Repository\AcquisitionFreeDownloadRepository;
use AudienceHero\Bundle\ActivityBundle\CollectionBuilder\EntityCollectionBuilderInterface;
use AudienceHero\Bundle\PodcastBundle\Event\PodcastEvents;
use AudienceHero\Bundle\PodcastBundle\Repository\PodcastChannelRepository;

/**
 * @author Marc Weistroff <marc@weistroff.net>
 */
class PodcastChannelCollectionBuilder implements EntityCollectionBuilderInterface
{
    /**
     * @var AcquisitionFreeDownloadRepository
     */
    private $repository;

    public function __construct(PodcastChannelRepository $repository)
    {
        $this->repository = $repository;
    }

    public function build(): array
    {
        $entities = [];
        $ids = $this->repository->getAllIds();

        foreach ($ids as $id) {
            $entities[$id['id']] = [
                PodcastEvents::CHANNEL_HIT,
                PodcastEvents::CHANNEL_FEED_HIT,
                PodcastEvents::EPISODE_ENCLOSURE_DOWNLOAD,
                PodcastEvents::EPISODE_HIT,
            ];
        }

        return $entities;
    }
}
