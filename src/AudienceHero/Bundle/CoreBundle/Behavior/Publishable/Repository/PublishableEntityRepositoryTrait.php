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

namespace AudienceHero\Bundle\CoreBundle\Behavior\Publishable\Repository;

use Doctrine\ORM\QueryBuilder;

/**
 * PrivacyRepositoryTrait.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 *
 * @deprecated
 */
trait PublishableEntityRepositoryTrait
{
    /**
     * @param string $privacy
     *
     * @return array
     *
     * @deprecated Refactor in favor of PublishablePublic
     */
    public function findByPrivacy(string $privacy)
    {
        /** @var QueryBuilder $qb */
        $qb = $this->_em->createQueryBuilder('a');

        $qb->select('a')
            ->where('a.privacy = :privacy')
            ->setParameter('privacy', $privacy);

        return $qb->getQuery()->getResult();
    }
}
