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

namespace AudienceHero\Bundle\MailingCampaignBundle\Repository;

use AudienceHero\Bundle\MailingCampaignBundle\Entity\Email;
use AudienceHero\Bundle\MailingCampaignBundle\Repository\Specification\EmailIsIdentifiableBy;
use Happyr\DoctrineSpecification\EntitySpecificationRepository;

class EmailRepository extends EntitySpecificationRepository
{
    public function persistAndFlush(Email $email): void
    {
        $this->_em->persist($email);
        $this->_em->flush();
    }

    public function findOneOrNullByIdentifier($id): ?Email
    {
        return $this->matchOneOrNullResult(new EmailIsIdentifiableBy($id));
    }
}
