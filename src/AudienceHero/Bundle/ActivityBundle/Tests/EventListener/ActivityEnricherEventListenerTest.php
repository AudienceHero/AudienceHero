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

namespace AudienceHero\Bundle\ActivityBundle\Tests\EventListener;

use AudienceHero\Bundle\ActivityBundle\Entity\Activity;
use AudienceHero\Bundle\ActivityBundle\EventListener\ActivityEventSubscriber;
use AudienceHero\Bundle\ActivityBundle\Queue\ActivityProducer;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\UnitOfWork;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;
use Psr\Log\LoggerInterface;

class ActivityEnricherEventListenerTest extends TestCase
{
    /** @var ObjectProphecy */
    private $publisher;
    /** @var ObjectProphecy */
    private $logger;
    /** @var ObjectProphecy */
    private $em;

    public function setUp()
    {
        $this->publisher = $this->prophesize(ActivityProducer::class);
        $this->logger = $this->prophesize(LoggerInterface::class);
        $this->em = $this->prophesize(EntityManagerInterface::class);
        $this->uow = $this->prophesize(UnitOfWork::class);
    }

    public function testPublisherIsntCalledWhenNoActivityIsRegistered()
    {
        $this->publisher->enrich(Argument::any())->shouldNotBeCalled();
        $listener = new ActivityEventSubscriber($this->publisher->reveal(), $this->logger->reveal());

        $postFlush = new PostFlushEventArgs($this->em->reveal());
        $listener->postFlush($postFlush);
    }

    public function testPublisherIsntCalledForOtherObjectThanActivity()
    {
        $this->publisher->enrich(Argument::any())->shouldNotBeCalled();

        $this->uow->getScheduledEntityInsertions()->willReturn([
            new \stdClass(),
            new \stdClass(),
        ]);
        $this->em->getUnitOfWork()->willReturn($this->uow->reveal())->shouldBeCalled();
        $listener = new ActivityEventSubscriber($this->publisher->reveal(), $this->logger->reveal());

        $onFlush = new OnFlushEventArgs($this->em->reveal());
        $listener->onFlush($onFlush);
        $postFlush = new PostFlushEventArgs($this->em->reveal());
        $listener->postFlush($postFlush);
    }

    public function testPublisherShouldBeCalledForEveryScheduledActivity()
    {
        $a1 = new Activity();
        $a2 = new Activity();

        $this->publisher->enrich($a1)->shouldBeCalled();
        $this->publisher->enrich($a2)->shouldBeCalled();

        $this->uow->getScheduledEntityInsertions()->willReturn([$a1, $a2]);
        $this->em->getUnitOfWork()->willReturn($this->uow->reveal())->shouldBeCalled();
        $listener = new ActivityEventSubscriber($this->publisher->reveal(), $this->logger->reveal());

        $onFlush = new OnFlushEventArgs($this->em->reveal());
        $listener->onFlush($onFlush);
        $postFlush = new PostFlushEventArgs($this->em->reveal());
        $listener->postFlush($postFlush);
    }

    public function testPublishedShouldLogAnyExceptionOccuring()
    {
        $a1 = new Activity();
        $e = new \Exception('Exception message');
        $this->publisher->enrich($a1)->shouldBeCalled()->willThrow($e);

        $this->logger->critical(Argument::cetera())->shouldBeCalled();

        $this->uow->getScheduledEntityInsertions()->willReturn([$a1]);
        $this->em->getUnitOfWork()->willReturn($this->uow->reveal())->shouldBeCalled();
        $listener = new ActivityEventSubscriber($this->publisher->reveal(), $this->logger->reveal());

        $onFlush = new OnFlushEventArgs($this->em->reveal());
        $listener->onFlush($onFlush);
        $postFlush = new PostFlushEventArgs($this->em->reveal());
        $listener->postFlush($postFlush);
    }
}
