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

namespace AudienceHero\Bundle\ContactImportCsvBundle\Queue\Processor;

use AudienceHero\Bundle\ContactBundle\Entity\ContactsGroup;
use AudienceHero\Bundle\ContactBundle\Manager\ContactManager;
use AudienceHero\Bundle\ContactBundle\Manager\OptManager;
use AudienceHero\Bundle\ContactImportCsvBundle\CSV\CsvReader;
use AudienceHero\Bundle\ContactImportCsvBundle\Factory\ContactFactory;
use AudienceHero\Bundle\ContactImportCsvBundle\Queue\ContactCsvImportProducer;
use AudienceHero\Bundle\ContactImportCsvBundle\Queue\ContactImportCsvMessage;
use AudienceHero\Bundle\CoreBundle\Event\CoreEvents;
use AudienceHero\Bundle\CoreBundle\Event\ImportEvent;
use AudienceHero\Bundle\CoreBundle\Serializer\MessageSerializer;
use Enqueue\Client\CommandSubscriberInterface;
use Enqueue\Consumption\Result;
use Interop\Queue\PsrContext;
use Interop\Queue\PsrMessage;
use Interop\Queue\PsrProcessor;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * ContactImportCsvProcessor.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
class ContactImportCsvProcessor implements PsrProcessor, CommandSubscriberInterface
{
    /**
     * @var MessageSerializer
     */
    private $serializer;
    /**
     * @var ContactFactory
     */
    private $factory;
    /**
     * @var ContactManager
     */
    private $contactManager;
    /**
     * @var OptManager
     */
    private $optManager;
    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;
    /**
     * @var RegistryInterface
     */
    private $registry;

    public function __construct(RegistryInterface $registry, ContactManager $contactManager, OptManager $optManager, ContactFactory $factory, MessageSerializer $serializer, EventDispatcherInterface $eventDispatcher)
    {
        $this->serializer = $serializer;
        $this->factory = $factory;
        $this->contactManager = $contactManager;
        $this->optManager = $optManager;
        $this->eventDispatcher = $eventDispatcher;
        $this->registry = $registry;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedCommand()
    {
        return [
            'processorName' => ContactCsvImportProducer::IMPORT_CSV_CONTACT,
            'queueName' => ContactCsvImportProducer::IMPORT_CSV_CONTACT,
            'queueNameHardcoded' => true,
            'exclusive' => true,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function process(PsrMessage $message, PsrContext $context)
    {
        /** @var ContactImportCsvMessage $message */
        $message = $this->serializer->deserialize($message->getBody(), ContactImportCsvMessage::class);
        if (!$message) {
            return new Result(static::REJECT, 'No Message');
        }
        $textStore = $message->getTextStore();
        if (!$textStore) {
            return new Result(static::REJECT, 'No TextStore found in message');
        }

        $contactsGroup = new ContactsGroup();
        $contactsGroup = $textStore->getSubject($contactsGroup->getSoftReferenceKey());

        $columns = $textStore->getMetadata()['contact_matches'];
        $csv = new CsvReader($textStore->getText());

        $failures = [];
        foreach ($csv->getRecords($columns) as $record) {
            $contact = $this->factory->createContact($textStore->getOwner(), $record);
            if (!$contact) {
                $failures[] = $record;
                continue;
            }

            $this->contactManager->add($contact);
            $cgc = $this->contactManager->addToGroup($contact, $contactsGroup);
            $this->optManager->optin($cgc);
        }
        $textStore->addMetadata('import_finished', true);
        $textStore->addMetadata('import_failures', $failures);

        $this->eventDispatcher->dispatch(CoreEvents::IMPORT_POST_LOAD, new ImportEvent($textStore));
        $this->registry->getManager()->flush();

        return Result::ACK;
    }
}
