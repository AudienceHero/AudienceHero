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

use AudienceHero\Bundle\MailingCampaignBundle\Entity\MailingRecipient;
use AudienceHero\Bundle\PromoBundle\Entity\Promo;
use Happyr\DoctrineSpecification\EntitySpecificationRepository;

/**
 * PromoRepository.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
class PromoRecipientRepository extends EntitySpecificationRepository
{
    public function findPending()
    {
        $qb = $this->createQueryBuilder('pr');

        return $qb->select('pr')
                  ->leftJoin('pr.mailingRecipient', 'prmr')
                  ->where('prmr.status = :status_pending')
                  ->setParameter('status_pending', MailingRecipient::STATUS_PENDING)
                  ->getQuery()
                  ->getResult();
    }

    public function findRemindableByPromo(Promo $promo)
    {
        $qb = $this->createQueryBuilder('pr');

        return $qb->where('pr.promo = :promo')
                  ->andWhere('pr.notInterested = false')
                  ->andWhere('pr.countDownload = 0')
                  ->andWhere('pr.reminderStatus is null')
                  ->setParameter('promo', $promo)
                  ->getQuery()
                  ->getResult();
    }

    public function findByPromoNotAttachedtoMailingRecipient(Promo $promo)
    {
        $qb = $this->createQueryBuilder('pr');

        return $qb->where('pr.promo = :promo')
                  ->andWhere('pr.mailingRecipient is null')
                  ->andWhere('pr.name is not null')
                  ->orderBy('pr.name', 'ASC')
                  ->setParameter('promo', $promo)
                  ->getQuery()
                  ->getResult();
    }
}
