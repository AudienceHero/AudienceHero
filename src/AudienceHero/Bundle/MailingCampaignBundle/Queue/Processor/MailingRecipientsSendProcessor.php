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
use AudienceHero\Bundle\MailingCampaignBundle\Entity\MailingRecipient;
use AudienceHero\Bundle\MailingCampaignBundle\Mailer\Mailer;
use AudienceHero\Bundle\MailingCampaignBundle\Queue\MailingMessage;
use AudienceHero\Bundle\MailingCampaignBundle\Queue\MailingProducer;
use Enqueue\Client\CommandSubscriberInterface;
use Enqueue\Consumption\Result;
use Interop\Queue\PsrContext;
use Interop\Queue\PsrMessage;
use Interop\Queue\PsrProcessor;
use Psr\Log\LoggerInterface;
use Swarrot\Broker\Message;

class MailingRecipientsSendProcessor implements PsrProcessor, CommandSubscriberInterface
{
    /**
     * @var \AudienceHero\Bundle\CoreBundle\Serializer\MessageSerializer
     */
    private $serializer;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var Mailer
     */
    private $mailer;

    public function __construct(MessageSerializer $serializer, Mailer $mailer, LoggerInterface $logger)
    {
        $this->serializer = $serializer;
        $this->logger = $logger;
        $this->mailer = $mailer;
    }

    /**
     * {@inheritdoc}
     */
    public function process(PsrMessage $psrMessage, PsrContext $context)
    {
        /** @var MailingMessage $message */
        $message = $this->serializer->deserialize($psrMessage->getBody(), MailingMessage::class);
        if (!$message) {
            return new Result(static::REJECT, 'No message');
        }

        if (!$message->getMailing()) {
            return new Result(static::REJECT, 'No Mailing in message');
        }

        $mr = $message->getMailingRecipient();
        if (!$mr) {
            return new Result(static::REJECT, 'No MailingRecipient in message.');
        }

        if (!$mr->getMailing()) {
            return new Result(static::REJECT, sprintf('No Mailing in MailingRecipient %s.', $mr->getId()));
        }

        if (!$mr->isStatusPending()) {
            return new Result(static::REJECT, sprintf('MailingRecipient %s is not in a pending status', $mr->getId()));
        }

        $mr->setStatus(Mailing::STATUS_DELIVERING);
        $this->mailer->send($mr);

        return new Result(static::ACK);
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedCommand()
    {
        return [
            'processorName' => MailingProducer::MAILING_RECIPIENT_SEND,
            'queueName' => MailingProducer::MAILING_RECIPIENT_SEND,
            'queueNameHardcoded' => true,
            'exclusive' => true,
        ];
    }
}
