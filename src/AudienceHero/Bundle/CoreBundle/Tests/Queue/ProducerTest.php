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

namespace AudienceHero\Bundle\CoreBundle\Tests\Queue;

use AudienceHero\Bundle\CoreBundle\Queue\Message;
use AudienceHero\Bundle\CoreBundle\Queue\Producer;
use Enqueue\Client\ProducerInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;
use Psr\Log\LoggerInterface;

class ProducerTest extends TestCase
{
    /** @var ObjectProphecy */
    private $serializer;
    /** @var ObjectProphecy */
    private $producer;
    /** @var ObjectProphecy */
    private $logger;

    public function setUp()
    {
        $this->serializer = $this->prophesize(\AudienceHero\Bundle\CoreBundle\Serializer\MessageSerializer::class);
        $this->producer = $this->prophesize(ProducerInterface::class);
        $this->logger = $this->prophesize(LoggerInterface::class);
        $this->message = new class() extends Message {
        };
    }

    public function testSendEvent()
    {
        $this->serializer->serialize($this->message)->willReturn('{"foo":"bar"}')->shouldBeCalled();
        $this->producer->sendEvent('event_name', '{"foo":"bar"}')->shouldBeCalled();

        $producer = new Producer($this->producer->reveal(), $this->serializer->reveal(), $this->logger->reveal());
        $producer->sendEvent('event_name', $this->message);
    }

    public function testSendCommand()
    {
        $this->serializer->serialize($this->message)->willReturn('{"foo":"bar"}')->shouldBeCalled();
        $this->producer->sendCommand('event_name', '{"foo":"bar"}', false)->shouldBeCalled();

        $producer = new Producer($this->producer->reveal(), $this->serializer->reveal(), $this->logger->reveal());
        $producer->sendCommand('event_name', $this->message);
    }
}
