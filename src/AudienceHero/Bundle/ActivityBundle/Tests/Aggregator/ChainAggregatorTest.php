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

use AudienceHero\Bundle\ActivityBundle\Aggregator\AggregatorInterface;
use AudienceHero\Bundle\ActivityBundle\Aggregator\ChainAggregator;
use AudienceHero\Bundle\ActivityBundle\Entity\Activity;
use AudienceHero\Bundle\ActivityBundle\Entity\Aggregate;
use AudienceHero\Bundle\ActivityBundle\Repository\AggregateRepository;
use AudienceHero\Bundle\CoreBundle\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;

class ChainAggregatorTest extends TestCase
{
    /** @var ObjectProphecy */
    private $registry;
    /** @var ObjectProphecy */
    private $em;
    /** @var ObjectProphecy */
    private $logger;
    /** @var ObjectProphecy */
    private $aggregator;
    /** @var ObjectProphecy */
    private $repository;

    public function setUp()
    {
        $this->registry = $this->prophesize(RegistryInterface::class);
        $this->repository = $this->prophesize(AggregateRepository::class);
        $this->em = $this->prophesize(EntityManagerInterface::class);
        $this->logger = $this->prophesize(LoggerInterface::class);
        $this->aggregator = $this->prophesize(AggregatorInterface::class);
    }

    public function getInstance()
    {
        $this->em->getRepository(Aggregate::class)->willReturn($this->repository->reveal());
        $this->registry->getManager()->willReturn($this->em->reveal());

        return new ChainAggregator($this->registry->reveal(), $this->logger->reveal());
    }

    public function testCompute()
    {
        $owner = new User();
        $subject = new Activity();
        $subject->setOwner($owner);

        $id = 'my_id';
        $type = 'my_type';

        $aggregate = new Aggregate();
        $aggregate->setSubjectId($id);
        $aggregate->setType($type);

        $this->repository->findOrCreateOneBySubjectIdAndType($id, $type)->shouldBeCalled()->willReturn($aggregate);

        $this->aggregator->supportsType()->shouldBeCalledTimes(1)->willReturn($type);
        $this->aggregator->supportsClass()->shouldBeCalledTimes(1)->willReturn(Activity::class);

        $this->em->find(Activity::class, $id)->shouldBeCalledTimes(1)->willReturn($subject);

        $this->aggregator->compute($aggregate)->shouldBeCalledTimes(1);
        $this->em->contains($aggregate)->shouldBeCalled()->willReturn(false);
        $this->em->persist($aggregate)->shouldBeCalled();
        $this->em->flush()->shouldBeCalled();
        $this->em->clear()->shouldBeCalled();

        $chain = $this->getInstance();
        $chain->addAggregator($this->aggregator->reveal());
        $chain->compute($id, $type);
        $this->assertSame($owner, $aggregate->getOwner());
    }

    public function testComputeSkipIfSubjectDoesNotExist()
    {
        $id = 'my_id';
        $type = 'my_type';

        $aggregate = new Aggregate();
        $aggregate->setSubjectId($id);
        $aggregate->setType($type);

        $this->repository->findOrCreateOneBySubjectIdAndType($id, $type)->shouldBeCalled()->willReturn($aggregate);

        $this->aggregator->supportsType()->shouldBeCalledTimes(1)->willReturn($type);
        $this->aggregator->supportsClass()->shouldBeCalledTimes(1)->willReturn(Activity::class);

        $this->em->find(Activity::class, $id)->shouldBeCalledTimes(1)->willReturn(null);

        $this->aggregator->compute($aggregate)->shouldNotBeCalled();
        $this->em->contains($aggregate)->shouldBeCalled()->willReturn(false);
        $this->em->persist($aggregate)->shouldBeCalled();
        $this->em->flush()->shouldBeCalled();
        $this->em->clear()->shouldBeCalled();

        $chain = $this->getInstance();
        $chain->addAggregator($this->aggregator->reveal());
        $chain->compute($id, $type);
    }
}
