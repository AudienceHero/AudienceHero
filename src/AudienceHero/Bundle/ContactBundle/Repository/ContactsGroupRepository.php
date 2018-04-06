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

namespace AudienceHero\Bundle\ContactBundle\Repository;

use AudienceHero\Bundle\CoreBundle\Behavior\Searchable\Repository\SearchableRepositoryTrait;
use Doctrine\ORM\Query;
use Happyr\DoctrineSpecification\EntitySpecificationRepository;

/**
 * ContactsGroupRepository.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
class ContactsGroupRepository extends EntitySpecificationRepository
{
    use SearchableRepositoryTrait;
}
