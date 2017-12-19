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

use AppBundle\Entity\Group;
use AudienceHero\Bundle\ContactBundle\Entity\Contact;
use AudienceHero\Bundle\ContactBundle\Entity\ContactsGroup;
use AudienceHero\Bundle\ContactBundle\Repository\Specification\ContactAndContactsGroup;
use Happyr\DoctrineSpecification\EntitySpecificationRepository;

/**
 * ContactsGroupContactRepository.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
class ContactsGroupContactRepository extends EntitySpecificationRepository
{
}
