<?php

/*
 * This file is part of the AudienceHero project.
 *
 * (c) Marc Weistroff <marc@weistroff.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AudienceHero\Bundle\ActivityBundle\Tests\Aggregator;

use ApiPlatform\Core\Api\IriConverterInterface;
use AudienceHero\Bundle\ActivityBundle\Aggregator\AggregateComputer;
use AudienceHero\Bundle\ActivityBundle\Entity\Activity;
use AudienceHero\Bundle\ActivityBundle\Repository\ActivityRepository;
use AudienceHero\Bundle\CoreBundle\Behavior\Identifiable\Finder\IdentifiableFinder;
use AudienceHero\Bundle\CoreBundle\Behavior\Referenceable\ReferenceableInterface;
use AudienceHero\Bundle\CoreBundle\Entity\User;
use Payum\Core\Registry\RegistryInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;

class AggregateComputerTest extends TestCase
{
    /** @var ObjectProphecy */
    private $repository;
    /** @var ObjectProphecy */
    private $finder;
    /** @var ObjectProphecy */
    private $iriConverter;

    public function setUp()
    {
        $this->repository = $this->prophesize(ActivityRepository::class);
        $this->finder = $this->prophesize(IdentifiableFinder::class);
        $this->iriConverter = $this->prophesize(IriConverterInterface::class);
    }

    private function getInstance(): AggregateComputer
    {
        return new AggregateComputer($this->repository->reveal(), $this->finder->reveal(), $this->iriConverter->reveal());
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Cannot compute aggregate for non-existing instance of Foobar with id my_id
     */
    public function testExceptionIsThrownIfNoSubjectIsFound()
    {
        $this->finder->find('Foobar', 'my_id')->shouldBeCalled()->willReturn(null);
        $this->getInstance()->countTotal('Foobar', 'my_id', 'type');
    }

    public function testCountTotal()
    {
        $subject = new Activity();
        $this->finder->find(Activity::class, 'id')->shouldBeCalled()->willReturn($subject);
        $this->iriConverter->getIriFromItem($subject)->shouldBeCalled()->willReturn('/api/foo');
        $this->repository->countTotal($subject->getSoftReferenceKey(), '/api/foo', 'type');

        $this->getInstance()->countTotal(Activity::class, 'id', 'type');
    }

    public function testCountDaily()
    {
        $subject = new Activity();
        $this->finder->find(Activity::class, 'id')->shouldBeCalled()->willReturn($subject);
        $this->iriConverter->getIriFromItem($subject)->shouldBeCalled()->willReturn('/api/foo');
        $this->repository->countDaily($subject->getSoftReferenceKey(), '/api/foo', 'type');

        $this->getInstance()->countDaily(Activity::class, 'id', 'type');
    }

    public function testCountField()
    {
        $subject = new Activity();
        $this->finder->find(Activity::class, 'id')->shouldBeCalled()->willReturn($subject);
        $this->iriConverter->getIriFromItem($subject)->shouldBeCalled()->willReturn('/api/foo');
        $this->repository->countField($subject->getSoftReferenceKey(), '/api/foo', 'type', 'field', 10);

        $this->getInstance()->countField(Activity::class, 'id', 'type', 'field', 10);
    }
}
