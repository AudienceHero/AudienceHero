<?php

/*
 * This file is part of the AudienceHero project.
 *
 * (c) Marc Weistroff <marc@weistroff.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AudienceHero\Bundle\ContactBundle\Tests\EventListener;

use AudienceHero\Bundle\ContactBundle\Entity\Contact;
use AudienceHero\Bundle\ContactBundle\Entity\ContactsGroup;
use AudienceHero\Bundle\ContactBundle\Entity\ContactsGroupContact;
use AudienceHero\Bundle\ContactBundle\Entity\ContactsGroupForm;
use AudienceHero\Bundle\ContactBundle\Event\ContactEvent;
use AudienceHero\Bundle\ContactBundle\Event\ContactEvents;
use AudienceHero\Bundle\ContactBundle\EventListener\OptinEventSubscriber;
use AudienceHero\Bundle\ContactBundle\Mailer\Model\OptinConfirmedEmail;
use AudienceHero\Bundle\ContactBundle\Mailer\Model\OptinRequestEmail;
use AudienceHero\Bundle\CoreBundle\Entity\User;
use AudienceHero\Bundle\CoreBundle\Mailer\TransactionalMailer;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;

class OptinEventSubscriberTest extends TestCase
{
    /** @var ObjectProphecy */
    private $mailer;
    /** @var ContactEvent */
    private $contactEvent;
    /** @var Contact */
    private $contact;
    /** @var ContactsGroup */
    private $contactsGroup;
    /** @var ContactsGroupContact */
    private $cgc;
    /** @var ContactsGroupForm */
    private $cgf;
    /** @var User */
    private $owner;

    public function setUp()
    {
        $this->mailer = $this->prophesize(TransactionalMailer::class);
        $this->owner = new User();
        $this->contact = new Contact();
        $this->contact->setOwner($this->owner);
        $this->contactsGroup = new ContactsGroup();
        $this->contactsGroup->setOwner($this->owner);
        $this->cgc = new ContactsGroupContact();
        $this->cgc->setOwner($this->owner);
        $this->cgf = new ContactsGroupForm();
        $this->cgf->setOwner($this->owner);

        $this->contactEvent = ContactEvent::create()
            ->setContact($this->contact)
            ->setContactsGroup($this->contactsGroup)
            ->setContactsGroupContact($this->cgc)
            ->setContactsGroupForm($this->cgf)
        ;
    }

    public function getInstance(): OptinEventSubscriber
    {
        return new OptinEventSubscriber($this->mailer->reveal());
    }

    public function getSubscribedEvents()
    {
        $this->assertSame(
            [
                ContactEvents::OPT_IN_REQUEST => 'onOptinRequest',
                ContactEvents::OPT_IN_CONFIRMED => 'onOptinConfirmed',
            ],
            OptinEventSubscriber::getSubscribedEvents()
        );
    }

    public function testOnOptinRequestNoOpIfContactHasNoEmail()
    {
        $this->mailer->send(Argument::any(), Argument::any(), Argument::any(), Argument::any())->shouldNotBeCalled();
        $this->getInstance()->onOptinRequest($this->contactEvent);
    }

    public function testOnOptinRequest()
    {
        $this->contact->setEmail('foobar@example.com');
        $this->mailer->send(
            OptinRequestEmail::class,
            $this->owner,
            [
                'contactsGroupContact' => $this->cgc,
                'contactsGroup' => $this->contactsGroup,
                'contactsGroupForm' => $this->cgf,
                'contact' => $this->contact,
            ],
            'foobar@example.com'
        )
            ->shouldBeCalled();

        $this->getInstance()->onOptinRequest($this->contactEvent);
    }

    public function testOnOptinConfirmedNoOpIfContactHasNoEmail()
    {
        $this->mailer->send(Argument::any(), Argument::any(), Argument::any(), Argument::any())->shouldNotBeCalled();
        $this->getInstance()->onOptinConfirmed($this->contactEvent);
    }

    public function testOnOptinConfirmed()
    {
        $this->contact->setEmail('foobar@example.com');
        $this->mailer->send(
            OptinConfirmedEmail::class,
            $this->owner,
            [
                'contactsGroupContact' => $this->cgc,
                'contactsGroup' => $this->contactsGroup,
                'contactsGroupForm' => $this->cgf,
                'contact' => $this->contact,
            ],
            'foobar@example.com'
        )
            ->shouldBeCalled();

        $this->getInstance()->onOptinConfirmed($this->contactEvent);
    }
}
