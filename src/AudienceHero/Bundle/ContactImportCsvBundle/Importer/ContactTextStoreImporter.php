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

namespace AudienceHero\Bundle\ContactImportCsvBundle\Importer;

use ApiPlatform\Core\Bridge\Symfony\Validator\Exception\ValidationException;
use AudienceHero\Bundle\ContactImportCsvBundle\Queue\ContactCsvImportProducer;
use AudienceHero\Bundle\ContactImportCsvBundle\Validator\Constraints\ColumnsMatch;
use AudienceHero\Bundle\CoreBundle\Entity\TextStore;
use AudienceHero\Bundle\CoreBundle\Importer\TextStoreImporterInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * ContactTextStoreImporter.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
class ContactTextStoreImporter implements TextStoreImporterInterface
{
    const DOCUMENT_TYPE = 'csv.contacts';

    /**
     * @var ValidatorInterface
     */
    private $validator;
    /**
     * @var ContactCsvImportProducer
     */
    private $producer;

    public function __construct(ValidatorInterface $validator, ContactCsvImportProducer $producer)
    {
        $this->validator = $validator;
        $this->producer = $producer;
    }

    public function supports(TextStore $textStore): bool
    {
        return self::DOCUMENT_TYPE === $textStore->getDocumentType();
    }

    public function import(TextStore $textStore): void
    {
        $violations = $this->validator->startContext()
            ->validate($textStore->getMetadata()['contact_matches'], new ColumnsMatch())
            ->getViolations();

        if (count($violations) > 0) {
            throw new ValidationException($violations);
        }

        $this->producer->import($textStore);
    }
}
