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

namespace AudienceHero\Bundle\MailingCampaignBundle\Queue\Processor;

use AudienceHero\Bundle\CoreBundle\Serializer\MessageSerializer;
use AudienceHero\Bundle\MailingCampaignBundle\Entity\Mailing;
use AudienceHero\Bundle\MailingCampaignBundle\Factory\MailingRecipientCollectionFactory;
use AudienceHero\Bundle\MailingCampaignBundle\Queue\MailingMessage;
use AudienceHero\Bundle\MailingCampaignBundle\Queue\MailingProducer;
use Enqueue\Client\CommandSubscriberInterface;
use Enqueue\Consumption\Result;
use Interop\Queue\PsrContext;
use Interop\Queue\PsrMessage;
use Interop\Queue\PsrProcessor;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;

class MailingSendProcessor implements PsrProcessor, CommandSubscriberInterface
{
    /**
     * @var RegistryInterface
     */
    private $registry;

    /**
     * @var MailingProducer
     */
    private $producer;

    /**
     * @var \AudienceHero\Bundle\CoreBundle\Serializer\MessageSerializer
     */
    private $serializer;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var MailingRecipientCollectionFactory
     */
    private $factory;

    public function __construct(MailingProducer $producer, RegistryInterface $registry, MailingRecipientCollectionFactory $factory, MessageSerializer $serializer, LoggerInterface $logger)
    {
        $this->producer = $producer;
        $this->serializer = $serializer;
        $this->registry = $registry;
        $this->logger = $logger;
        $this->factory = $factory;
    }

    /**
     * {@inheritdoc}
     */
    public function process(PsrMessage $message, PsrContext $context)
    {
        /** @var MailingMessage $message */
        $message = $this->serializer->deserialize($message->getBody(), MailingMessage::class);
        if (!$message) {
            return new Result(static::REJECT, 'No Message');
        }
        $mailing = $message->getMailing();
        if (!$mailing) {
            return new Result(static::REJECT, 'No Mailing found in message');
        }

        if (!$mailing->isStatusPending()) {
            return new Result(static::REJECT, sprintf('Mailing is in status different from PENDING, skipping send. (status=%s)', $mailing->getStatus()));
        }

        $mailing->setStatus(Mailing::STATUS_DELIVERING);
        $em = $this->registry->getEntityManager();

        $mrs = [];
        if (!$message->isBoost()) {
            $mrs = $this->factory->createCollection($mailing);
        } else {
            $mrs = $this->factory->createBoostCollection($mailing);
        }

        $this->logger->info(sprintf('%s mailing %s to %s recipients.', $message->isBoost() ? 'Boost' : 'Send', $mailing->getId(), count($mrs)));
        foreach ($mrs as $mr) {
            $em->persist($mr);
        }
        $em->flush();
        foreach ($mrs as $mr) {
            $this->producer->sendMailingRecipient($mr);
        }

        return new Result(static::ACK);
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedCommand()
    {
        return [
            'processorName' => MailingProducer::MAILING_SEND,
            'queueName' => MailingProducer::MAILING_SEND,
            'queueNameHardcoded' => true,
            'exclusive' => true,
        ];
    }
}
