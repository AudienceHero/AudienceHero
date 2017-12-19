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
use AudienceHero\Bundle\MailingCampaignBundle\Entity\Mailing;
use AudienceHero\Bundle\PromoBundle\Factory\PromoRecipientCollectionFactory;
use AudienceHero\Bundle\PromoBundle\Queue\PromoMessage;
use AudienceHero\Bundle\PromoBundle\Queue\PromoProducer;
use Enqueue\Client\CommandSubscriberInterface;
use Enqueue\Consumption\Result;
use Interop\Queue\PsrContext;
use Interop\Queue\PsrMessage;
use Interop\Queue\PsrProcessor;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * PromosSendProcessor.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
class PromosSendProcessor implements PsrProcessor, CommandSubscriberInterface
{
    /**
     * @var PromoProducer
     */
    private $producer;
    /**
     * @var RegistryInterface
     */
    private $registry;
    /**
     * @var MessageSerializer
     */
    private $serializer;
    /**
     * @var PromoRecipientCollectionFactory
     */
    private $collectionFactory;

    public function __construct(PromoProducer $producer, RegistryInterface $registry, MessageSerializer $serializer, PromoRecipientCollectionFactory $collectionFactory)
    {
        $this->producer = $producer;
        $this->registry = $registry;
        $this->serializer = $serializer;
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedCommand()
    {
        return [
            'processorName' => PromoProducer::PROMO_SEND,
            'queueName' => PromoProducer::PROMO_SEND,
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

        $promo = $message->getPromo();
        if (!$promo) {
            return new Result(static::REJECT, 'No Promo found in message');
        }

        $mailing = $message->isBoost() ? $promo->getMailing()->getBoostMailing() : $promo->getMailing();
        if (!$mailing) {
            return new Result(static::REJECT, 'No mailing in Promo found in message');
        }

        if (!$mailing->isStatusPending()) {
            return new Result(static::REJECT, sprintf('Mailing in Promo is in status different from PENDING, skipping send. (status=%s)', $mailing->getStatus()));
        }

        $mailing->setStatus(Mailing::STATUS_DELIVERING);
        $em = $this->registry->getEntityManager();

        $promoRecipients = [];
        if (!$message->isBoost()) {
            $promoRecipients = $this->collectionFactory->createCollection($promo);
            foreach ($promoRecipients as $promoRecipient) {
                $em->persist($promoRecipient);
            }
        } else {
            $promoRecipients = $this->collectionFactory->createBoostCollection($promo);
        }
        $em->flush();

        foreach ($promoRecipients as $promoRecipient) {
            if (!$message->isBoost()) {
                $this->producer->sendPromoRecipient($promoRecipient);
            } else {
                $this->producer->boostPromoRecipient($promoRecipient);
            }
        }

        return Result::ACK;
    }
}
