<?php

/*
 * This file is part of the AudienceHero project.
 *
 * (c) Marc Weistroff <marc@weistroff.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AudienceHero\Bundle\ActivityBundle\Tests\Queue\Processor;

use AudienceHero\Bundle\ActivityBundle\Enricher\EnricherInterface;
use AudienceHero\Bundle\ActivityBundle\Entity\Activity;
use AudienceHero\Bundle\ActivityBundle\Event\ActivityEvent;
use AudienceHero\Bundle\ActivityBundle\Event\ActivityEvents;
use AudienceHero\Bundle\ActivityBundle\Queue\ActivityMessage;
use AudienceHero\Bundle\ActivityBundle\Queue\ActivityProducer;
use AudienceHero\Bundle\ActivityBundle\Queue\Processor\ActivityEnrichProcessor;
use AudienceHero\Bundle\CoreBundle\Serializer\MessageSerializer;
use Doctrine\ORM\EntityManagerInterface;
use Enqueue\Consumption\Result;
use Enqueue\Null\NullContext;
use Enqueue\Null\NullMessage;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ActivityEnrichProcessorTest extends TestCase
{
    /** @var ObjectProphecy */
    private $serializer;
    /** @var ObjectProphecy */
    private $enricher;
    /** @var ObjectProphecy */
    private $eventDispatcher;
    /** @var ObjectProphecy */
    private $registry;
    /** @var ObjectProphecy */
    private $em;

    public function setUp()
    {
        $this->serializer = $this->prophesize(MessageSerializer::class);
        $this->enricher = $this->prophesize(EnricherInterface::class);
        $this->eventDispatcher = $this->prophesize(EventDispatcherInterface::class);
        $this->registry = $this->prophesize(RegistryInterface::class);
        $this->em = $this->prophesize(EntityManagerInterface::class);
    }

    private function getInstance(): ActivityEnrichProcessor
    {
        return new ActivityEnrichProcessor(
            $this->serializer->reveal(),
            $this->enricher->reveal(),
            $this->eventDispatcher->reveal(),
            $this->registry->reveal()
        );
    }

    public function testGetSubscribedCommand()
    {
        $this->assertSame(
            [
                'processorName' => ActivityProducer::ACTIVITY_ENRICH,
                'queueName' => ActivityProducer::ACTIVITY_ENRICH,
                'queueNameHardcoded' => true,
                'exclusive' => true,
            ],
            ActivityEnrichProcessor::getSubscribedCommand()
        );
    }

    public function testProcessFailsWhenMessageIsIncorrect()
    {
        $this->serializer->deserialize('foobar', ActivityMessage::class)->willReturn(null);
        $result = $this->getInstance()->process(new NullMessage('foobar'), new NullContext());
        $this->assertInstanceOf(Result::class, $result);
        $this->assertSame(Result::REJECT, $result->getStatus());
        $this->assertSame('No message', $result->getReason());
    }

    public function testProcessFailsWhenMessageDoesNotContainAnActivity()
    {
        $this->serializer->deserialize('foobar', ActivityMessage::class)
            ->willReturn(ActivityMessage::create());
        $result = $this->getInstance()->process(new NullMessage('foobar'), new NullContext());
        $this->assertInstanceOf(Result::class, $result);
        $this->assertSame(Result::REJECT, $result->getStatus());
        $this->assertSame('No Activity in message', $result->getReason());
    }

    public function testProcessEnrichActivity()
    {
        $activity = new Activity();
        $this->serializer->deserialize('foobar', ActivityMessage::class)
            ->willReturn(ActivityMessage::create()->setActivity($activity));

        $this->enricher->enrich($activity)->shouldBeCalled();
        $this->eventDispatcher->dispatch(
            ActivityEvents::POST_ENRICH,
            Argument::that(function(ActivityEvent $event) use ($activity) {
                return $activity === $event->getActivity();
            })
        );
        $this->em->flush()->shouldBeCalled();
        $this->registry->getEntityManager()->shouldBeCalled()->willReturn($this->em->reveal());

        $result = $this->getInstance()->process(new NullMessage('foobar'), new NullContext());
        $this->assertSame(Result::ACK, $result);
    }
}
