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

namespace AudienceHero\Bundle\AcquisitionFreeDownloadBundle\Bridge\AudienceHero\ActivityBundle\CollectionBuilder;

use AudienceHero\Bundle\AcquisitionFreeDownloadBundle\Events\AcquisitionFreeDownloadEvents;
use AudienceHero\Bundle\AcquisitionFreeDownloadBundle\Repository\AcquisitionFreeDownloadRepository;
use AudienceHero\Bundle\ActivityBundle\CollectionBuilder\EntityCollectionBuilderInterface;

/**
 * AcquisitionFreeDownloadCollectionBuilder.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
class AcquisitionFreeDownloadCollectionBuilder implements EntityCollectionBuilderInterface
{
    /**
     * @var AcquisitionFreeDownloadRepository
     */
    private $repository;

    public function __construct(AcquisitionFreeDownloadRepository $repository)
    {
        $this->repository = $repository;
    }

    public function build(): array
    {
        $entities = [];
        $ids = $this->repository->getAllIds();

        foreach ($ids as $id) {
            $entities[$id['id']] = [
                AcquisitionFreeDownloadEvents::HIT,
                AcquisitionFreeDownloadEvents::UNLOCK,
            ];
        }

        return $entities;
    }
}
