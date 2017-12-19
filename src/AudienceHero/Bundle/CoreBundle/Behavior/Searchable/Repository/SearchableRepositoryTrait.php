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

namespace AudienceHero\Bundle\CoreBundle\Behavior\Searchable\Repository;

use AudienceHero\Bundle\CoreBundle\Entity\Person;
use Doctrine\ORM\QueryBuilder;

/**
 * SearchableRepositoryTrait.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
trait SearchableRepositoryTrait
{
    public function getSearchQueryBuilder(Person $owner, string $query)
    {
        /** @var QueryBuilder $qb */
        $qb = $this->createQueryBuilder('e');
        $qb->where('e.owner = :owner')
            ->andWhere('PLAINTO_TSQUERY(e.tsv, :query) = true')
            ->setParameter('owner', $owner)
            ->setParameter('query', $query)
        ;
    }
}
