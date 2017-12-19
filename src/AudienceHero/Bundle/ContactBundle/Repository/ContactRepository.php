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

use AppBundle\Domain\CsvMatch;
use AppBundle\Entity\Person;
use AudienceHero\Bundle\ContactBundle\Entity\Contact;
use AudienceHero\Bundle\ContactImportCsvBundle\CSV\CsvReader;
use AudienceHero\Bundle\CoreBundle\Repository\SearchableRepositoryTrait;
use Doctrine\ORM\NoResultException;
use Happyr\DoctrineSpecification\EntitySpecificationRepository;

/**
 * ContactRepository.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
class ContactRepository extends EntitySpecificationRepository
{
    use SearchableRepositoryTrait;
}
