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

namespace AudienceHero\Bundle\ContactImportCsvBundle\EventListener;

use AudienceHero\Bundle\ContactImportCsvBundle\CSV\ColumnsMatcher;
use AudienceHero\Bundle\ContactImportCsvBundle\CSV\CsvReader;
use AudienceHero\Bundle\ContactImportCsvBundle\Importer\ContactTextStoreImporter;
use AudienceHero\Bundle\CoreBundle\Entity\TextStore;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * TextStoreEventSubscriber.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
class TextStoreEventSubscriber implements EventSubscriber
{
    /**
     * @var ValidatorInterface
     */
    private $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    /** {@inheritdoc} */
    public function getSubscribedEvents(): array
    {
        return [
            'prePersist',
        ];
    }

    private function getEntityFromEventArgs(LifecycleEventArgs $eventArgs): ?TextStore
    {
        $entity = $eventArgs->getEntity();
        if (!$entity instanceof TextStore) {
            return null;
        }

        if (TextStore::CONTENT_TYPE_CSV !== $entity->getContentType()) {
            return null;
        }

        if (ContactTextStoreImporter::DOCUMENT_TYPE !== $entity->getDocumentType()) {
            return null;
        }

        return $entity;
    }

    public function prePersist(LifecycleEventArgs $eventArgs): void
    {
        $entity = $this->getEntityFromEventArgs($eventArgs);
        if (!$entity) {
            return;
        }

        $csv = new CsvReader($entity->getText());
        $matcher = new ColumnsMatcher($csv->getHeader());

        $entity->addMetadata('header', $csv->getHeader());
        $entity->addMetadata('contact_choices', ColumnsMatcher::getChoices());
        $entity->addMetadata('sample', $csv->extractSample(5));
        $entity->addMetadata('contact_matches', $matcher->getMatches());
    }
}
