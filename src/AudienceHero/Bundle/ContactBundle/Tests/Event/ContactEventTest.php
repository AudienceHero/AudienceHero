<?php

/*
 * This file is part of the AudienceHero project.
 *
 * (c) Marc Weistroff <marc@weistroff.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AudienceHero\Bundle\ContactBundle\Tests\Event;

use AudienceHero\Bundle\ContactBundle\Entity\Contact;
use AudienceHero\Bundle\ContactBundle\Entity\ContactsGroup;
use AudienceHero\Bundle\ContactBundle\Entity\ContactsGroupContact;
use AudienceHero\Bundle\ContactBundle\Entity\ContactsGroupForm;
use AudienceHero\Bundle\ContactBundle\Event\ContactEvent;
use PHPUnit\Framework\TestCase;

class ContactEventTest extends TestCase
{
    public function testAccessors()
    {
        $event = ContactEvent::create();
        $this->assertInstanceOf(ContactEvent::class, $event);

        $contact = new Contact();
        $cg = new ContactsGroup();
        $cgc = new ContactsGroupContact();
        $cgf = new ContactsGroupForm();

        $this->assertNull($event->getContact($contact));
        $this->assertNull($event->getContactsGroup($cg));
        $this->assertNull($event->getContactsGroupContact($cgc));
        $this->assertNull($event->getContactsGroupForm($cgf));

        $this->assertSame($event, $event->setContact($contact));
        $this->assertSame($event, $event->setContactsGroup($cg));
        $this->assertSame($event, $event->setContactsGroupContact($cgc));
        $this->assertSame($event, $event->setContactsGroupForm($cgf));

        $this->assertSame($contact, $event->getContact($contact));
        $this->assertSame($cg, $event->getContactsGroup($cg));
        $this->assertSame($cgc, $event->getContactsGroupContact($cgc));
        $this->assertSame($cgf, $event->getContactsGroupForm($cgf));
    }
}
