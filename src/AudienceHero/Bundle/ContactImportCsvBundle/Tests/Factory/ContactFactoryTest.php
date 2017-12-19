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

namespace AudienceHero\Bundle\ContactImportCsvBundle\Tests\Factory;

use AudienceHero\Bundle\ContactBundle\Entity\Contact;
use AudienceHero\Bundle\ContactImportCsvBundle\CSV\ColumnsMatcher;
use AudienceHero\Bundle\ContactImportCsvBundle\Factory\ContactFactory;
use AudienceHero\Bundle\CoreBundle\Entity\User;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;
use Psr\Log\LoggerInterface;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ContactFactoryTest extends TestCase
{
    /** @var ObjectProphecy */
    private $logger;
    /** @var ObjectProphecy */
    private $validator;

    public function setUp()
    {
        $this->validator = $this->prophesize(ValidatorInterface::class);
        $this->logger = $this->prophesize(LoggerInterface::class);
    }

    public function testCreateContactWithFirstAndLastName()
    {
        $this->validator->validate(Argument::type(Contact::class))->willReturn(
            new ConstraintViolationList()
        )->shouldBeCalled();
        $owner = new User();

        $factory = new ContactFactory($this->validator->reveal(), $this->logger->reveal());

        $data = [
            ColumnsMatcher::COLUMN_FIRST_NAME => 'Marc',
            ColumnsMatcher::COLUMN_LAST_NAME => 'W',
            ColumnsMatcher::COLUMN_CITY => 'Lyon',
            ColumnsMatcher::COLUMN_COUNTRY => 'France',
            ColumnsMatcher::COLUMN_NOTES => 'No Notes',
            ColumnsMatcher::COLUMN_EMAIL => 'marc@example.com',
            ColumnsMatcher::COLUMN_PHONE => '+336273',
            ColumnsMatcher::COLUMN_POSTAL_CODE => '69006',
            ColumnsMatcher::COLUMN_HOMEPAGE => 'http://www.audiencehero.org',
            ColumnsMatcher::COLUMN_SALUTATION_NAME => 'Marc',
        ];

        $contact = $factory->createContact($owner, $data);
        $this->assertInstanceOf(Contact::class, $contact);
        $this->assertSame($owner, $contact->getOwner());
        $this->assertSame('Marc W', $contact->getName());
        $this->assertSame('Lyon', $contact->getCity());
        $this->assertSame('FR', $contact->getCountry());
        $this->assertSame('No Notes', $contact->getNotes());
        $this->assertSame('marc@example.com', $contact->getEmail());
        $this->assertSame('+336273', $contact->getPhone());
        $this->assertSame('69006', $contact->getPostalCode());
        $this->assertSame('http://www.audiencehero.org', $contact->getHomepage());
        $this->assertSame('Marc', $contact->getSalutationName());
    }

    public function testCreateContactWithFullName()
    {
        $data = [ColumnsMatcher::COLUMN_FULL_NAME => 'Marc'];
        $this->validator->validate(Argument::type(Contact::class))->willReturn(
            new ConstraintViolationList()
        )->shouldBeCalled();
        $owner = new User();

        $factory = new ContactFactory($this->validator->reveal(), $this->logger->reveal());
        $contact = $factory->createContact($owner, $data);
        $this->assertEquals('Marc', $contact->getName());
    }

    public function testCreateContactReturnsNullIfEntityDoesNotValidate()
    {
        $this->validator->validate(Argument::type(Contact::class))->willReturn(
            new ConstraintViolationList([$this->prophesize(ConstraintViolation::class)->reveal()])
        )->shouldBeCalled();

        $factory = new ContactFactory($this->validator->reveal(), $this->logger->reveal());
        $contact = $factory->createContact(new User(), []);
        $this->assertNull($contact);
    }
}
