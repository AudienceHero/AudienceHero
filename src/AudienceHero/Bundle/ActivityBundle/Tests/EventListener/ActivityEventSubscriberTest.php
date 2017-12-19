<?php

/*
 * This file is part of the AudienceHero project.
 *
 * (c) Marc Weistroff <marc@weistroff.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AudienceHero\Bundle\ActivityBundle\Tests\EventListener;

use AudienceHero\Bundle\ActivityBundle\Entity\Activity;
use AudienceHero\Bundle\ActivityBundle\EventListener\ActivityEventSubscriber;
use AudienceHero\Bundle\ActivityBundle\Queue\ActivityProducer;
use AudienceHero\Bundle\CoreBundle\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\UnitOfWork;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;
use Psr\Log\LoggerInterface;

class ActivityEventSubscriberTest extends TestCase
{
    /** @var ObjectProphecy */
    private $producer;

    /** @var ObjectProphecy */
    private $logger;
    /** @var ObjectProphecy */
    private $em;
    /** @var ObjectProphecy */
    private $uow;

    public function setUp()
    {
        $this->producer = $this->prophesize(ActivityProducer::class);
        $this->logger = $this->prophesize(LoggerInterface::class);

        $this->em = $this->prophesize(EntityManagerInterface::class);
        $this->uow = $this->prophesize(UnitOfWork::class);
    }

    private function getInstance(): ActivityEventSubscriber
    {
        return new ActivityEventSubscriber($this->producer->reveal(), $this->logger->reveal());
    }

    public function testSubscribedEvents()
    {
        $this->assertSame([
            'onFlush',
            'onPostFlush',
        ],
            $this->getInstance()->getSubscribedEvents()
        );
    }

    public function testSubscriber()
    {
        $a1 = new Activity();
        $a2 = new Activity();
        $user = new User();

        $this->uow->getScheduledEntityInsertions()->willReturn([
            $a1, $a2, $user,
        ]);

        $this->em->getUnitOfWork()->willReturn($this->uow->reveal());
        $this->producer->enrich($a1)->shouldBeCalled();
        $this->producer->enrich($a2)->shouldBeCalled();
        $this->producer->enrich($user)->shouldNotBeCalled();

        $instance = $this->getInstance();
        $instance->onFlush(new OnFlushEventArgs($this->em->reveal()));
        $instance->postFlush(new PostFlushEventArgs($this->em->reveal()));
    }

    public function testErrorIsLogged()
    {
        $a1 = new Activity();

        $this->uow->getScheduledEntityInsertions()->willReturn([$a1]);

        $this->em->getUnitOfWork()->willReturn($this->uow->reveal());
        $exception = new \RuntimeException('foo');
        $this->producer->enrich($a1)->shouldBeCalled()->willThrow($exception);
        $this->logger->critical('foo', ['e' => $exception])->shouldBeCalled();

        $instance = $this->getInstance();
        $instance->onFlush(new OnFlushEventArgs($this->em->reveal()));
        $instance->postFlush(new PostFlushEventArgs($this->em->reveal()));
    }
}
