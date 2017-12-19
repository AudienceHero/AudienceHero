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

namespace AudienceHero\Bundle\PromoBundle\Queue\Processor;

use AudienceHero\Bundle\CoreBundle\Serializer\MessageSerializer;
use AudienceHero\Bundle\MailingCampaignBundle\Entity\MailingRecipient;
use AudienceHero\Bundle\PromoBundle\Mailer\Mailer;
use AudienceHero\Bundle\PromoBundle\Queue\PromoMessage;
use AudienceHero\Bundle\PromoBundle\Queue\PromoProducer;
use Enqueue\Client\CommandSubscriberInterface;
use Enqueue\Consumption\Result;
use Interop\Queue\PsrContext;
use Interop\Queue\PsrMessage;
use Interop\Queue\PsrProcessor;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * PromoRecipientsSendProcessor.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
class PromoRecipientsSendProcessor implements PsrProcessor, CommandSubscriberInterface
{
    /**
     * @var MessageSerializer
     */
    private $serializer;
    /**
     * @var Mailer
     */
    private $mailer;
    /**
     * @var RegistryInterface
     */
    private $registry;

    public function __construct(MessageSerializer $serializer, Mailer $mailer, RegistryInterface $registry)
    {
        $this->serializer = $serializer;
        $this->mailer = $mailer;
        $this->registry = $registry;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedCommand()
    {
        return [
            'processorName' => PromoProducer::PROMO_RECIPIENT_SEND,
            'queueName' => PromoProducer::PROMO_RECIPIENT_SEND,
            'queueNameHardcoded' => true,
            'exclusive' => true,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function process(PsrMessage $message, PsrContext $context)
    {
        /** @var PromoMessage $message */
        $message = $this->serializer->deserialize($message->getBody(), PromoMessage::class);
        if (!$message) {
            return new Result(static::REJECT, 'No Message');
        }

        $promoRecipient = $message->getPromoRecipient();
        if (!$promoRecipient) {
            return new Result(Result::REJECT, 'No PromoRecipient in message');
        }

        if (!$message->isBoost() && MailingRecipient::STATUS_PENDING !== $promoRecipient->getMailingRecipient()->getStatus()) {
            return new Result(Result::REJECT, sprintf('PromoRecipient is in a status different than PENDING (=%s)', $promoRecipient->getMailingRecipient()->getStatus()));
        }

        if ($message->isBoost() && MailingRecipient::STATUS_PENDING !== $promoRecipient->getBoostMailingRecipient()->getStatus()) {
            return new Result(Result::REJECT, sprintf('Boost PromoRecipient is in a status different than PENDING (=%s)', $promoRecipient->getBoostMailingRecipient()->getStatus()));
        }

        $this->mailer->send($promoRecipient, $message->isBoost());
        if (!$message->isBoost()) {
            $promoRecipient->getMailingRecipient()->setStatus(MailingRecipient::STATUS_SENT);
        } else {
            $promoRecipient->getBoostMailingRecipient()->setStatus(MailingRecipient::STATUS_SENT);
        }
        $this->registry->getManager()->flush();

        return Result::ACK;
    }
}
