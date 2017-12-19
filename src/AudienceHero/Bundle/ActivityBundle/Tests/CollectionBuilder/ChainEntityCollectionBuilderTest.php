<?php

/*
 * This file is part of the AudienceHero project.
 *
 * (c) Marc Weistroff <marc@weistroff.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AudienceHero\Bundle\ActivityBundle\Tests\CollectionBuilder;

use AudienceHero\Bundle\ActivityBundle\CollectionBuilder\ChainEntityCollectionBuilder;
use AudienceHero\Bundle\ActivityBundle\CollectionBuilder\EntityCollectionBuilderInterface;
use PHPUnit\Framework\TestCase;

class ChainEntityCollectionBuilderTest extends TestCase
{
    public function testChain()
    {
        $b1 = $this->prophesize(EntityCollectionBuilderInterface::class);
        $b2 = $this->prophesize(EntityCollectionBuilderInterface::class);

        $b1->build()->shouldBeCalled()->willReturn(
            [
                '5ff1477a-b4fe-48ce-91af-1492867fcbc0' => ['TYPE1', 'TYPE2'],
                'fdb2ff08-4a58-4cf4-9f8f-6ffb4be6bbb5' => ['TYPE2'],
            ]
        );

        $b2->build()->shouldBeCalled()->willReturn(
            [
                '5ff1477a-b4fe-48ce-91af-1492867fcbc0' => ['TYPE3', 'TYPE4'],
                'fdb2ff08-4a58-4cf4-9f8f-6ffb4be6bbb5' => ['TYPE3'],
                '7386b2d1-f2fe-417a-9974-db6bf23d6d9c' => ['TYPE3'],
            ]
        );

        $chain = new ChainEntityCollectionBuilder();
        $chain->addCollectionBuilder($b1->reveal());
        $chain->addCollectionBuilder($b2->reveal());

        $this->assertSame([
                '5ff1477a-b4fe-48ce-91af-1492867fcbc0' => ['TYPE1', 'TYPE2', 'TYPE3', 'TYPE4'],
                'fdb2ff08-4a58-4cf4-9f8f-6ffb4be6bbb5' => ['TYPE2', 'TYPE3'],
                '7386b2d1-f2fe-417a-9974-db6bf23d6d9c' => ['TYPE3'],
        ],
            $chain->build()
        );
    }
}
