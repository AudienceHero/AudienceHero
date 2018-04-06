<?php

/*
 * This file is part of the AudienceHero project.
 *
 * (c) Marc Weistroff <marc@weistroff.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AudienceHero\Bundle\AcquisitionFreeDownloadBundle\Tests\Repository;

use AudienceHero\Bundle\AcquisitionFreeDownloadBundle\Repository\AcquisitionFreeDownloadRepository;
use AudienceHero\Bundle\ActivityBundle\Repository\EntityCollectionBuilderRepositoryTrait;
use AudienceHero\Bundle\CoreBundle\Behavior\Publishable\Repository\PublishableEntityRepositoryTrait;
use AudienceHero\Bundle\CoreBundle\Behavior\Searchable\Repository\SearchableRepositoryTrait;
use PHPUnit\Framework\TestCase;

class AcquisitionFreeDownloadRepositoryTest extends TestCase
{
    public function testTraits()
    {
        $rc = new \ReflectionClass(AcquisitionFreeDownloadRepository::class);
        $this->assertTrue(in_array(EntityCollectionBuilderRepositoryTrait::class, $rc->getTraitNames()));
        $this->assertTrue(in_array(SearchableRepositoryTrait::class, $rc->getTraitNames()));
        $this->assertTrue(in_array(PublishableEntityRepositoryTrait::class, $rc->getTraitNames()));
    }
}
