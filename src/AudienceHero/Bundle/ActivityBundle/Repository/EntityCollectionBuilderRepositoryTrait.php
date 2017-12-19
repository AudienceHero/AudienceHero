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

namespace AudienceHero\Bundle\ActivityBundle\Repository;

use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;

/**
 * EntityCollectionBuilderRepositoryTrait.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
trait EntityCollectionBuilderRepositoryTrait
{
    public function getAllIds()
    {
        /** @var QueryBuilder $qb */
        $qb = $this->createQueryBuilder('o');

        return $qb->select('o.id')->getQuery()->getResult(Query::HYDRATE_ARRAY);
    }
}
