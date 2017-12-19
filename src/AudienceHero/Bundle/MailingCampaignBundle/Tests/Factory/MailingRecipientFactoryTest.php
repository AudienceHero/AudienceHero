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

namespace AudienceHero\Bundle\MailingCampaignBundle\Tests\Factory;

use AudienceHero\Bundle\ContactBundle\Entity\Contact;
use AudienceHero\Bundle\ContactBundle\Entity\ContactsGroupContact;
use AudienceHero\Bundle\CoreBundle\Entity\User;
use AudienceHero\Bundle\MailingCampaignBundle\Entity\Mailing;
use AudienceHero\Bundle\MailingCampaignBundle\Entity\MailingRecipient;
use AudienceHero\Bundle\MailingCampaignBundle\Factory\MailingRecipientFactory;
use PHPUnit\Framework\TestCase;

class MailingRecipientFactoryTest extends TestCase
{
    public function testCreate()
    {
        $owner = new User();
        $mailing = new Mailing();
        $mailing->setOwner($owner);

        $contact = new Contact();
        $contact->setOwner($owner);
        $contact->setName('Foo Bar');
        $contact->setEmail('foobar@example.com');
        $contact->setSalutationName('Fooby');
        $cgc = new ContactsGroupContact();
        $cgc->setContact($contact);

        $factory = new MailingRecipientFactory();
        $mr = $factory->create($mailing, $cgc);
        $this->assertInstanceOf(MailingRecipient::class, $mr);
        $this->assertSame($owner, $mr->getOwner());
        $this->assertSame($mailing, $mr->getMailing());
        $this->assertSame('Fooby', $mr->getSalutationName());
        $this->assertSame('Foo Bar', $mr->getToName());
        $this->assertSame('foobar@example.com', $mr->getToEmail());
        $this->assertSame($cgc, $mr->getContactsGroupContact());
    }
}
