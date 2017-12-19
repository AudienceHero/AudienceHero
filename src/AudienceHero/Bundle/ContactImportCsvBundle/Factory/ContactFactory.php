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

namespace AudienceHero\Bundle\ContactImportCsvBundle\Factory;

use AudienceHero\Bundle\ContactBundle\Entity\Contact;
use AudienceHero\Bundle\ContactImportCsvBundle\CSV\ColumnsMatcher;
use AudienceHero\Bundle\CoreBundle\Entity\Person;
use Psr\Log\LoggerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * ContactFactory.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
class ContactFactory
{
    /**
     * @var ValidatorInterface
     */
    private $validator;
    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(ValidatorInterface $validator, LoggerInterface $logger)
    {
        $this->validator = $validator;
        $this->logger = $logger;
    }

    public function createContact(Person $owner, array $record): ?Contact
    {
        $contact = new Contact();
        $contact->setOwner($owner);

        $firstName = null;
        $lastName = null;
        foreach ($record as $key => $value) {
            switch ($key) {
                case ColumnsMatcher::COLUMN_FIRST_NAME:
                    $firstName = $value;
                    break;
                case ColumnsMatcher::COLUMN_LAST_NAME:
                    $lastName = $value;
                    break;
                case ColumnsMatcher::COLUMN_CITY:
                    $contact->setCity($value);
                    break;
                case ColumnsMatcher::COLUMN_COUNTRY:
                    $contact->setCountry($value);
                    break;
                case ColumnsMatcher::COLUMN_FULL_NAME:
                    $contact->setName($value);
                    break;
                case ColumnsMatcher::COLUMN_NOTES:
                    $contact->setNotes($value);
                    break;
                case ColumnsMatcher::COLUMN_EMAIL:
                    $contact->setEmail($value);
                    break;
                case ColumnsMatcher::COLUMN_PHONE:
                    $contact->setPhone($value);
                    break;
                case ColumnsMatcher::COLUMN_SALUTATION_NAME:
                    $contact->setSalutationName($value);
                    break;
                case ColumnsMatcher::COLUMN_POSTAL_CODE:
                    $contact->setPostalCode($value);
                    break;
                case ColumnsMatcher::COLUMN_ADDRESS:
                    $contact->setAddress($value);
                    break;
                case ColumnsMatcher::COLUMN_HOMEPAGE:
                    $contact->setHomepage($value);
                    break;
                case ColumnsMatcher::COLUMN_COMPANY_NAME:
                    $contact->setCompanyName($value);
                    break;
            }
        }

        if ($firstName || $lastName) {
            $contact->setName(trim(sprintf('%s %s', $firstName, $lastName)));
        }

        if (count($violations = $this->validator->validate($contact)) > 0) {
            $this->logger->debug('Created Contact instance is not valid.', ['contact' => $contact, 'violations' => $violations]);

            return null;
        }

        return $contact;
    }
}
