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

namespace AudienceHero\Bundle\FileBundle\Repository;

use AudienceHero\Bundle\CoreBundle\Behavior\Searchable\Repository\SearchableRepositoryTrait;
use AudienceHero\Bundle\FileBundle\Entity\File;
use Happyr\DoctrineSpecification\EntitySpecificationRepository;

/**
 * FileRepository.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
class FileRepository extends EntitySpecificationRepository
{
    use SearchableRepositoryTrait;

    public function findProcessable(): array
    {
        $qb = $this->createQueryBuilder('f');
        $qb->select('f')
            ->where('f.filetype = :type_audio AND (f.waveform IS NULL OR f.duration IS NULL OR f.transcoded128 IS NULL)')
            ->orWhere('f.filetype = :type_image')
            ->setParameter('type_audio', 'audio')
            ->setParameter('type_image', 'image')
        ;

        return $qb->getQuery()->getResult();
    }
}
