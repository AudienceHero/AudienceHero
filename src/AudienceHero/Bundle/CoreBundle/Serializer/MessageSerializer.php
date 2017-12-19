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

namespace AudienceHero\Bundle\CoreBundle\Serializer;

use AudienceHero\Bundle\CoreBundle\Queue\Message;
use Psr\Log\LoggerInterface;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * MessageSerializer.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
class MessageSerializer
{
    /** @var SerializerInterface */
    private $serializer;
    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(SerializerInterface $serializer, LoggerInterface $logger)
    {
        $this->serializer = $serializer;
        $this->logger = $logger;
    }

    public function serialize(Message $message): string
    {
        try {
            return $this->serializer->serialize($message, 'json', ['enable_max_depth' => true]);
        } catch (\Exception $e) {
            $this->logger->critical('Errore while serializing message.', ['exception' => $e, 'message' => $message]);

            throw $e;
        }
    }

    public function deserialize(string $data, string $type): ?Message
    {
        try {
            return $this->serializer->deserialize($data, $type, 'json');
        } catch (\Exception $e) {
            $this->logger->critical('Errore while deserializing message.', ['exception' => $e, 'data' => $data, 'type' => $type]);

            return null;
        }
    }
}
