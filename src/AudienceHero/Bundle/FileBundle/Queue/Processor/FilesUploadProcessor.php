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

namespace AudienceHero\Bundle\FileBundle\Queue\Processor;

use AudienceHero\Bundle\CoreBundle\Serializer\MessageSerializer;
use AudienceHero\Bundle\FileBundle\Entity\File;
use AudienceHero\Bundle\FileBundle\ETL\Workflow;
use AudienceHero\Bundle\FileBundle\Queue\FileMessage;
use AudienceHero\Bundle\FileBundle\Queue\FileProducer;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Enqueue\Client\CommandSubscriberInterface;
use Enqueue\Consumption\Result;
use Interop\Queue\PsrContext;
use Interop\Queue\PsrMessage;
use Interop\Queue\PsrProcessor;

class FilesUploadProcessor implements PsrProcessor, CommandSubscriberInterface
{
    /**
     * @var MessageSerializer
     */
    private $serializer;

    /**
     * @var Workflow
     */
    private $workflow;
    /**
     * @var Registry
     */
    private $registry;

    public function __construct(MessageSerializer $serializer, Workflow $workflow, Registry $registry)
    {
        $this->serializer = $serializer;
        $this->workflow = $workflow;
        $this->registry = $registry;
    }

    public function process(PsrMessage $message, PsrContext $context)
    {
        /** @var FileMessage $message */
        $message = $this->serializer->deserialize($message->getBody(), FileMessage::class);
        if (!$message) {
            return new Result(Result::REJECT, 'No message');
        }

        /** @var File $file */
        $file = $message->getFile();
        if (!$file) {
            return new Result(Result::REJECT, 'No File found in message');
        }

        $this->workflow->run($file);
        $this->registry->getManager()->flush();

        return PsrProcessor::ACK;
    }

    public static function getSubscribedCommand()
    {
        return [
            'processorName' => FileProducer::FILE_UPLOAD,
            'queueName' => FileProducer::FILE_UPLOAD,
            'queueNameHardcoded' => true,
            'exclusive' => true,
        ];
    }
}
