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

namespace AudienceHero\Bundle\CoreBundle\Queue;

use AudienceHero\Bundle\CoreBundle\Serializer\MessageSerializer;
use Enqueue\Client\ProducerInterface;
use Psr\Log\LoggerInterface;

/**
 * Producer.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
class Producer
{
    private $serializer;
    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(ProducerInterface $producer, MessageSerializer $serializer, LoggerInterface $logger)
    {
        $this->serializer = $serializer;
        $this->producer = $producer;
        $this->logger = $logger;
    }

    public function sendEvent($eventName, Message $message)
    {
        $msg = $this->serializer->serialize($message);
        $this->logger->debug('Sending event', ['eventName' => $eventName, 'body' => $msg]);
        $this->producer->sendEvent($eventName, $msg);
    }

    public function sendCommand($eventName, Message $message)
    {
        $msg = $this->serializer->serialize($message);
        $this->logger->debug('Sending command', ['eventName' => $eventName, 'body' => $msg]);
        $this->producer->sendCommand($eventName, $msg, false);
    }
}
