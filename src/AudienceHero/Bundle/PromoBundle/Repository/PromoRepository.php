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

namespace AudienceHero\Bundle\PromoBundle\Repository;

use AppBundle\Repository\NonUniqueResultException;
use AudienceHero\Bundle\CoreBundle\Entity\Person;
use AudienceHero\Bundle\MailingCampaignBundle\Entity\Mailing;
use Doctrine\ORM\NoResultException;
use Happyr\DoctrineSpecification\EntitySpecificationRepository;

/**
 * PromoRepository.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
class PromoRepository extends EntitySpecificationRepository
{
}
