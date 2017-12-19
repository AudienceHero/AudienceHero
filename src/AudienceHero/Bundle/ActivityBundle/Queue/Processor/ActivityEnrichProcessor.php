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

namespace AudienceHero\Bundle\ActivityBundle\Queue\Processor;

use AppBundle\Swarrot\Message;
use AudienceHero\Bundle\ActivityBundle\Enricher\EnricherInterface;
use AudienceHero\Bundle\ActivityBundle\Event\ActivityEvent;
use AudienceHero\Bundle\ActivityBundle\Event\ActivityEvents;
use AudienceHero\Bundle\ActivityBundle\Queue\ActivityMessage;
use AudienceHero\Bundle\ActivityBundle\Queue\ActivityProducer;
use AudienceHero\Bundle\CoreBundle\Serializer\MessageSerializer;
use Enqueue\Client\CommandSubscriberInterface;
use Enqueue\Consumption\Result;
use Interop\Queue\PsrContext;
use Interop\Queue\PsrMessage;
use Interop\Queue\PsrProcessor;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ActivityEnrichProcessor implements PsrProcessor, CommandSubscriberInterface
{
    /** @var EnricherInterface */
    private $enricher;
    /** @var EventDispatcherInterface */
    private $eventDispatcher;
    /**
     * @var \AudienceHero\Bundle\CoreBundle\Serializer\MessageSerializer
     */
    private $serializer;
    /**
     * @var RegistryInterface
     */
    private $registry;

    public function __construct(MessageSerializer $serializer, EnricherInterface $enricher, EventDispatcherInterface $eventDispatcher, RegistryInterface $registry)
    {
        $this->enricher = $enricher;
        $this->eventDispatcher = $eventDispatcher;
        $this->serializer = $serializer;
        $this->registry = $registry;
    }

    /**
     * {@inheritdoc}
     */
    public function process(PsrMessage $message, PsrContext $context)
    {
        /** @var ActivityMessage $message */
        $message = $this->serializer->deserialize($message->getBody(), ActivityMessage::class);
        if (!$message) {
            return new Result(Result::REJECT, 'No message');
        }

        if (!$activity = $message->getActivity()) {
            return new Result(Result::REJECT, 'No Activity in message');
        }

        $this->enricher->enrich($activity);
        $this->eventDispatcher->dispatch(ActivityEvents::POST_ENRICH, new ActivityEvent($activity));
        $this->registry->getEntityManager()->flush();

        return Result::ACK;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedCommand()
    {
        return [
            'processorName' => ActivityProducer::ACTIVITY_ENRICH,
            'queueName' => ActivityProducer::ACTIVITY_ENRICH,
            'queueNameHardcoded' => true,
            'exclusive' => true,
        ];
    }
}
