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

use AudienceHero\Bundle\AcquisitionFreeDownloadBundle\Entity\AcquisitionFreeDownload;
use AudienceHero\Bundle\ActivityBundle\Entity\Activity;
use AudienceHero\Bundle\MailingCampaignBundle\Entity\Mailing;
use AudienceHero\Bundle\PodcastBundle\Entity\PodcastChannel;
use AudienceHero\Bundle\PodcastBundle\Entity\PodcastEpisode;
use Happyr\DoctrineSpecification\EntitySpecificationRepository;

/**
 * ContactRepository.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
class ActivityRepository extends EntitySpecificationRepository
{
    public function countTotal(string $softReferenceKey, string $iri, string $type): int
    {
        $qb = $this->createCountQueryBuilder($softReferenceKey, $iri, $type);

        return $qb->getQuery()->getSingleScalarResult();
    }

    public function countDaily(string $softReferenceKey, string $iri, string $type): array
    {
        $qb = $this->createCountQueryBuilder($softReferenceKey, $iri, $type);

        $qb->select('DATE(a.createdAt) as date, count(a) as nb')
           ->groupBy('date')
           ->orderBy('date', 'DESC');

        $result = $qb->getQuery()->getResult();

        return array_combine(array_column($result, 'date'), array_column($result, 'nb'));
    }

    public function countField(string $softReferenceKey, string $iri, string $type, string $field, int $limit): array
    {
        $qb = $this->createCountQueryBuilder($softReferenceKey, $iri, $type);

        $qb->select(sprintf('a.%s as field, count(a) AS nb', $field))
            ->groupBy('field')
            ->orderBy('nb', 'DESC')
            ->setMaxResults($limit);

        $result = $qb->getQuery()->getResult();

        return array_combine(array_column($result, 'field'), array_column($result, 'nb'));
    }

    private function createCountQueryBuilder(string $softReferenceKey, string $iri, string $type)
    {
        $qb = $this->createQueryBuilder('a');
        $subject = json_encode([$softReferenceKey => $iri]);

        return $qb->select('count(a)')
                  ->where('a.type = :type')
                  ->andWhere('jsonb_contains(a.subjects, :subject) = true')
                  ->andWhere('a.isSpam != true')
                  ->setParameter('type', $type)
                  ->setParameter('subject', $subject)
        ;
    }

    public function persist(Activity $activity)
    {
        $this->getEntityManager()->persist($activity);
    }

    public function flush()
    {
        $this->getEntityManager()->flush();
    }
}
