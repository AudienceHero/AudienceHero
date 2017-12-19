<?php

/*
 * This file is part of the AudienceHero project.
 *
 * (c) Marc Weistroff <marc@weistroff.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AudienceHero\Bundle\AcquisitionFreeDownloadBundle\Tests\Bridge\AudienceHero\ActivityBundle\CollectionBuilder;

use AudienceHero\Bundle\AcquisitionFreeDownloadBundle\Bridge\AudienceHero\ActivityBundle\CollectionBuilder\AcquisitionFreeDownloadCollectionBuilder;
use AudienceHero\Bundle\AcquisitionFreeDownloadBundle\Events\AcquisitionFreeDownloadEvents;
use AudienceHero\Bundle\AcquisitionFreeDownloadBundle\Repository\AcquisitionFreeDownloadRepository;
use PHPUnit\Framework\TestCase;

class AcquisitionFreeDownloadCollectionBuilderTest extends TestCase
{
    public function testBuild()
    {
        $repository = $this->prophesize(AcquisitionFreeDownloadRepository::class);
        $repository->getAllIds()->shouldBeCalled()->willReturn([['id' => 'id1'], ['id' => 'id2']]);

        $builder = new AcquisitionFreeDownloadCollectionBuilder($repository->reveal());
        $this->assertSame(
            [
                'id1' => [
                    AcquisitionFreeDownloadEvents::HIT,
                    AcquisitionFreeDownloadEvents::UNLOCK,
                ],
                'id2' => [
                    AcquisitionFreeDownloadEvents::HIT,
                    AcquisitionFreeDownloadEvents::UNLOCK,
                ],
            ],
            $builder->build()
        );
    }
}
