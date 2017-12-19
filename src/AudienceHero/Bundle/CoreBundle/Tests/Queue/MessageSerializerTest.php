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
use AudienceHero\Bundle\CoreBundle\Serializer\MessageSerializer;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;
use Psr\Log\LoggerInterface;
use Symfony\Component\Serializer\SerializerInterface;

class MessageSerializerTest extends TestCase
{
    /** @var ObjectProphecy */
    private $serializer;
    /** @var ObjectProphecy */
    private $logger;

    public function setUp()
    {
        $this->serializer = $this->prophesize(SerializerInterface::class);
        $this->logger = $this->prophesize(LoggerInterface::class);
    }

    public function testSerialize()
    {
        $message = new class() extends Message {
        };
        $this->serializer->serialize($message, 'json', ['enable_max_depth' => true])->shouldBeCalled()->willReturn('{}');

        $serializer = new MessageSerializer($this->serializer->reveal(), $this->logger->reveal());
        $result = $serializer->serialize($message);
        $this->assertSame('{}', $result);
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Foo
     */
    public function testSerializeLogErrorAndThrowsException()
    {
        $message = new class() extends Message {
        };
        $exception = new \RuntimeException('Foo');
        $this->serializer->serialize($message, 'json', ['enable_max_depth' => true])
                         ->shouldBeCalled()
                         ->willThrow($exception);

        $this->logger->critical(Argument::any(), ['exception' => $exception, 'message' => $message]);

        $serializer = new \AudienceHero\Bundle\CoreBundle\Serializer\MessageSerializer($this->serializer->reveal(), $this->logger->reveal());
        $serializer->serialize($message);
    }

    public function testDeserialize()
    {
        $message = new class() extends Message {
        };
        $this->serializer->deserialize('{}', get_class($message), 'json')->shouldBeCalled()->willReturn($message);

        $serializer = new MessageSerializer($this->serializer->reveal(), $this->logger->reveal());
        $result = $serializer->deserialize('{}', get_class($message));
        $this->assertSame($message, $result);
    }

    public function testDeserializeReturnsNullInCaseOfException()
    {
        $message = new class() extends Message {
        };
        $exception = new \RuntimeException('Foo');
        $this->serializer->deserialize('{}', get_class($message), 'json')
                         ->shouldBeCalled()
                         ->willThrow($exception);

        $this->logger->critical(Argument::any(), ['exception' => $exception, 'data' => '{}', 'type' => get_class($message)]);

        $serializer = new MessageSerializer($this->serializer->reveal(), $this->logger->reveal());
        $result = $serializer->deserialize('{}', get_class($message));
        $this->assertNull($result);
    }
}
