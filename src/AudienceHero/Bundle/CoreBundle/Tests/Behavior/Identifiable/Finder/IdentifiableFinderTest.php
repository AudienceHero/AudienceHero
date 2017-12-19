<?php

/*
 * This file is part of the AudienceHero project.
 *
 * (c) Marc Weistroff <marc@weistroff.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AudienceHero\Bundle\CoreBundle\Tests\Behavior\Identifiable\Finder;

use AudienceHero\Bundle\CoreBundle\Behavior\Identifiable\Finder\IdentifiableFinder;
use AudienceHero\Bundle\CoreBundle\Behavior\Identifiable\IdentifiableInterface;
use Doctrine\ORM\EntityRepository;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Bridge\Doctrine\RegistryInterface;

class IdentifiableFinderTest extends TestCase
{
    public function testFind()
    {
        $identifiable = $this->prophesize(IdentifiableInterface::class)->reveal();

        $repository = $this->prophesize(EntityRepository::class);
        $repository->find('id')->willReturn($identifiable);

        /** @var ObjectProphecy $registry */
        $registry = $this->prophesize(RegistryInterface::class);
        $registry->getRepository('class')->shouldBeCalled()->willReturn($repository->reveal());

        $finder = new IdentifiableFinder($registry->reveal());
        $this->assertSame($identifiable, $finder->find('class', 'id'));
    }
}
