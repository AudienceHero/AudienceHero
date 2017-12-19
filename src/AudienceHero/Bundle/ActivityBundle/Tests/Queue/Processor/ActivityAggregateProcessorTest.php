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

use AudienceHero\Bundle\ActivityBundle\Aggregator\ChainAggregator;
use AudienceHero\Bundle\ActivityBundle\Entity\Activity;
use AudienceHero\Bundle\ActivityBundle\Queue\ActivityMessage;
use AudienceHero\Bundle\ActivityBundle\Queue\ActivityProducer;
use AudienceHero\Bundle\ActivityBundle\Queue\Processor\ActivityAggregateProcessor;
use AudienceHero\Bundle\CoreBundle\Entity\User;
use AudienceHero\Bundle\CoreBundle\Serializer\MessageSerializer;
use Enqueue\Consumption\Result;
use Enqueue\Null\NullContext;
use Enqueue\Null\NullMessage;
use PHPUnit\Framework\TestCase;

class ActivityAggregateProcessorTest extends TestCase
{
    /** ObjectProphecy */
    private $serializer;
    /** ObjectProphecy */
    private $chainAggregator;

    public function setUp()
    {
        $this->serializer = $this->prophesize(MessageSerializer::class);
        $this->chainAggregator = $this->prophesize(ChainAggregator::class);
    }

    private function getInstance(): ActivityAggregateProcessor
    {
        return new ActivityAggregateProcessor($this->serializer->reveal(), $this->chainAggregator->reveal());
    }

    public function testGetSubscribedCommand()
    {
        $this->assertSame([
            'processorName' => ActivityProducer::ACTIVITY_AGGREGATE,
            'queueName' => ActivityProducer::ACTIVITY_AGGREGATE,
            'queueNameHardcoded' => true,
            'exclusive' => true,
            ],
        ActivityAggregateProcessor::getSubscribedCommand()
        );
    }

    public function testProcessRejectsUndecodableMessage()
    {
        $this->serializer->deserialize('{foobar}', ActivityMessage::class)
            ->shouldBeCalled()
            ->willReturn(null);

        $result = $this->getInstance()->process(new NullMessage('{foobar}'), new NullContext());
        $this->assertInstanceOf(Result::class, $result);
        $this->assertSame(Result::REJECT, $result->getStatus());
        $this->assertSame('No message', $result->getReason());
    }

    public function testProcessRejectsMessageWithoutAnActivity()
    {
        $this->serializer->deserialize('{foobar}', ActivityMessage::class)
            ->shouldBeCalled()
            ->willReturn(new ActivityMessage());

        $result = $this->getInstance()->process(new NullMessage('{foobar}'), new NullContext());
        $this->assertInstanceOf(Result::class, $result);
        $this->assertSame(Result::REJECT, $result->getStatus());
        $this->assertSame('No Activity in message', $result->getReason());
    }

    public function testProcess()
    {
        $owner = new User();
        $activity = new Activity();
        $activity->setType('my_type');

        $subject = $this->prophesize(Activity::class);
        $subject->getId()->shouldBeCalled()->willReturn('id1');
        $subject->getSoftReferenceKey()->willReturn('actvities');
        $subject->getOwner()->willReturn($owner);

        $activity->addSubject($subject->reveal());

        $this->chainAggregator->compute('id1', 'my_type')->shouldBeCalled();

        $this->serializer->deserialize('{foobar}', ActivityMessage::class)
            ->shouldBeCalled()
            ->willReturn((new ActivityMessage())->setActivity($activity));

        $result = $this->getInstance()->process(new NullMessage('{foobar}'), new NullContext());
        $this->assertSame(Result::ACK, $result);
    }
}
