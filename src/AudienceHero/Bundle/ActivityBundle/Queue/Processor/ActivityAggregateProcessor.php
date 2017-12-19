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
use AudienceHero\Bundle\ActivityBundle\Aggregator\ChainAggregator;
use AudienceHero\Bundle\ActivityBundle\Queue\ActivityMessage;
use AudienceHero\Bundle\ActivityBundle\Queue\ActivityProducer;
use AudienceHero\Bundle\CoreBundle\Serializer\MessageSerializer;
use Enqueue\Client\CommandSubscriberInterface;
use Enqueue\Consumption\Result;
use Interop\Queue\PsrContext;
use Interop\Queue\PsrMessage;
use Interop\Queue\PsrProcessor;

class ActivityAggregateProcessor implements PsrProcessor, CommandSubscriberInterface
{
    /**
     * @var MessageSerializer
     */
    private $serializer;
    /**
     * @var ChainAggregator
     */
    private $chainAggregator;

    public function __construct(MessageSerializer $serializer, ChainAggregator $chainAggregator)
    {
        $this->serializer = $serializer;
        $this->chainAggregator = $chainAggregator;
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

        // We don't know which type matches which class/entity so we have
        // to try to compute for each object. The aggregators will discard the computation by themselves.
        foreach ($activity->getSubjects() as $subject) {
            $this->chainAggregator->compute($subject->getId(), $activity->getType());
        }

        return Result::ACK;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedCommand()
    {
        return [
            'processorName' => ActivityProducer::ACTIVITY_AGGREGATE,
            'queueName' => ActivityProducer::ACTIVITY_AGGREGATE,
            'queueNameHardcoded' => true,
            'exclusive' => true,
        ];
    }
}
