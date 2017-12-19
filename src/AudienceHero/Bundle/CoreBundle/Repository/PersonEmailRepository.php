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

namespace AudienceHero\Bundle\CoreBundle\Repository;

use AudienceHero\Bundle\CoreBundle\Entity\Person;
use Happyr\DoctrineSpecification\EntitySpecificationRepository;

class PersonEmailRepository extends EntitySpecificationRepository
{
    public function findByOwner(Person $owner)
    {
        return $this->getByOwnerQueryBuilder($owner)->getQuery()->getResult();
    }

    public function getByOwnerQueryBuilder(Person $owner)
    {
        $qb = $this->createQueryBuilder('pe');
        $qb = $qb->select('pe')
                  ->where('pe.owner = :owner')
                  ->orderBy('pe.email', 'ASC')
                  ->setParameter('owner', $owner);

        return $qb;
    }
}
