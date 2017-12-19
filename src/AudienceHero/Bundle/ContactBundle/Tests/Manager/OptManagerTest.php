<?php

/*
 * This file is part of the AudienceHero project.
 *
 * (c) Marc Weistroff <marc@weistroff.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AudienceHero\Bundle\ContactBundle\Tests\Manager;

use AudienceHero\Bundle\ContactBundle\Entity\Contact;
use AudienceHero\Bundle\ContactBundle\Entity\ContactsGroup;
use AudienceHero\Bundle\ContactBundle\Entity\ContactsGroupContact;
use AudienceHero\Bundle\ContactBundle\Entity\ContactsGroupForm;
use AudienceHero\Bundle\ContactBundle\Event\ContactEvent;
use AudienceHero\Bundle\ContactBundle\Event\ContactEvents;
use AudienceHero\Bundle\ContactBundle\Manager\OptManager;
use AudienceHero\Bundle\CoreBundle\Entity\User;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class OptManagerTest extends TestCase
{
    /** @var ObjectProphecy */
    private $eventDispatcher;

    /** @var User */
    private $owner;
    /** @var Contact */
    private $contact;
    /** @var ContactsGroup */
    private $contactsGroup;
    /** @var ContactsGroupContact */
    private $contactsGroupContact;
    /** @var ContactsGroupForm */
    private $contactsGroupForm;

    public function setUp()
    {
        $this->eventDispatcher = $this->prophesize(EventDispatcherInterface::class);
        $this->owner = new User();
        $this->contact = new Contact();
        $this->contact->setOwner($this->owner);
        $this->contactsGroup = new ContactsGroup();
        $this->contactsGroup->setOwner($this->owner);
        $this->contactsGroupContact = new ContactsGroupContact();
        $this->contactsGroupContact->setOwner($this->owner);
        $this->contactsGroupForm = new ContactsGroupForm();
        $this->contactsGroupForm->setOwner($this->owner);

        $this->contactsGroupContact->setContact($this->contact);
        $this->contactsGroupContact->setGroup($this->contactsGroup);
        $this->contactsGroupForm->setContactsGroup($this->contactsGroup);
    }

    private function getInstance(): OptManager
    {
        return new OptManager($this->eventDispatcher->reveal());
    }

    public function testDispatchOptInRequest()
    {
        $this->eventDispatcher->dispatch(
            ContactEvents::OPT_IN_REQUEST,
            Argument::that(function(ContactEvent $event) {
                return
                    $event->getContact() === $this->contact  &&
                    $event->getContactsGroupContact() === $this->contactsGroupContact  &&
                    $event->getContactsGroupForm() === $this->contactsGroupForm &&
                    $event->getContactsGroup() === $this->contactsGroup
                ;
        }))->shouldBeCalled();

        $this->getInstance()->dispatchOptInRequest($this->contactsGroupContact, $this->contactsGroupForm);
    }

    public function testDispatchOptInConfirmed()
    {
        $this->eventDispatcher->dispatch(
            ContactEvents::OPT_IN_CONFIRMED,
            Argument::that(function(ContactEvent $event) {
                return
                    $event->getContact() === $this->contact  &&
                    $event->getContactsGroupContact() === $this->contactsGroupContact  &&
                    $event->getContactsGroupForm() === $this->contactsGroupForm &&
                    $event->getContactsGroup() === $this->contactsGroup
                    ;
            }))->shouldBeCalled();

        $this->getInstance()->dispatchOptInConfirmed($this->contactsGroupContact, $this->contactsGroupForm);
    }

    public function testOptIn()
    {
        $manager = $this->getInstance();
        $manager->cleanForSpam($this->contactsGroupContact);
        $manager->optin($this->contactsGroupContact);

        $this->assertNull($this->contactsGroupContact->getCleanedReason());
        $this->assertNull($this->contactsGroupContact->getCleanedAt());
        $this->assertNull($this->contactsGroupContact->getUnsubscribedAt());
        $this->assertNotnull($this->contactsGroupContact->getOptinAt());

        $this->getInstance()->optin($this->contactsGroupContact);
    }

    public function testOptOut()
    {
        $this->eventDispatcher->dispatch(
            ContactEvents::OPT_OUT,
            Argument::that(function(ContactEvent $event) {
                return
                    $event->getContact() === $this->contact  &&
                    $event->getContactsGroupContact() === $this->contactsGroupContact  &&
                    $event->getContactsGroup() === $this->contactsGroup
                    ;
            }))->shouldBeCalled();

        $manager = $this->getInstance();
        $manager->optin($this->contactsGroupContact);
        $manager->optout($this->contactsGroupContact);
        $this->assertNotNull($this->contactsGroupContact->getUnsubscribedAt());
    }

    public function testCleanForHardBounce()
    {
        $this->getInstance()->cleanForHardBounce($this->contactsGroupContact);
        $this->assertSame((new \DateTime())->format('r'), $this->contactsGroupContact->getCleanedAt()->format('r'));
        $this->assertSame(ContactsGroupContact::CLEAN_REASON_HARD_BOUNCE, $this->contactsGroupContact->getCleanedReason());
    }

    public function testCleanForSpam()
    {
        $this->getInstance()->cleanForSpam($this->contactsGroupContact);
        $this->assertSame((new \DateTime())->format('r'), $this->contactsGroupContact->getCleanedAt()->format('r'));
        $this->assertSame(ContactsGroupContact::CLEAN_REASON_SPAM, $this->contactsGroupContact->getCleanedReason());
    }
}
