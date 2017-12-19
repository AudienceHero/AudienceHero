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

namespace AudienceHero\Bundle\MailingCampaignBundle\Tests\EventListener;

use AudienceHero\Bundle\ContactBundle\Entity\Contact;
use AudienceHero\Bundle\ContactBundle\Entity\ContactsGroupContact;
use AudienceHero\Bundle\ContactBundle\Manager\OptManager;
use AudienceHero\Bundle\CoreBundle\Entity\User;
use AudienceHero\Bundle\MailingCampaignBundle\Entity\Email;
use AudienceHero\Bundle\MailingCampaignBundle\Entity\EmailEvent;
use AudienceHero\Bundle\MailingCampaignBundle\Entity\MailingRecipient;
use AudienceHero\Bundle\MailingCampaignBundle\EventListener\EmailEventMailingRecipientUpdaterEventSubscriber;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Event\LifecycleEventArgs;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;

class EmailEventMailingRecipientUpdaterEventSubscriberTest extends TestCase
{
    /** @var MailingRecipient */
    private $mailingRecipient;
    /** @var ObjectProphecy */
    private $manager;
    /** @var ObjectProphecy */
    private $optManager;
    /** @var Email */
    private $email;
    /** @var ContactsGroupContact */
    private $contactsGroupContact;

    public function setUp()
    {
        $owner = new User();
        $this->optManager = $this->prophesize(OptManager::class);
        $this->manager = $this->prophesize(EntityManager::class);

        $contact = new Contact();
        $contact->setOwner($owner);
        $contact->setName('Foo');
        $contact->setEmail('foo@example.com');
        $this->contactsGroupContact = new ContactsGroupContact();
        $this->contactsGroupContact->setOwner($owner);
        $this->contactsGroupContact->setContact($contact);
        $this->mailingRecipient = new MailingRecipient();
        $this->mailingRecipient->setOwner($owner);
        $this->mailingRecipient->setContactsGroupContact($this->contactsGroupContact);
        $this->email = new Email();
        $this->email->setOwner($owner);
        $this->email->setMailingRecipient($this->mailingRecipient);
    }

    public function testGetSubscribedEvent()
    {
        $subscriber = new EmailEventMailingRecipientUpdaterEventSubscriber($this->optManager->reveal());
        $this->assertSame(['postPersist'], $subscriber->getSubscribedEvents());
    }

    public function testPostPersistAbortsIfObjectIsNotAnEmailEvent()
    {
        $object = new User();

        $this->manager->flush()->shouldNotBeCalled();
        $eventArgs = new LifecycleEventArgs($object, $this->manager->reveal());
        $subscriber = new EmailEventMailingRecipientUpdaterEventSubscriber($this->optManager->reveal());
        $subscriber->postPersist($eventArgs);
    }

    public function testPostPersistWithSendEvent()
    {
        $emailEvent = $this->createEmailEvent(EmailEvent::EVENT_SEND);
        $this->callSubscriber($emailEvent);

        $this->assertSame(MailingRecipient::STATUS_SENT, $this->mailingRecipient->getStatus());
    }

    public function testPostPersistWithOpenEvent()
    {
        $emailEvent = $this->createEmailEvent(EmailEvent::EVENT_OPEN);
        $this->assertSame(0, $this->mailingRecipient->getMailOpenCounter());
        $this->callSubscriber($emailEvent);

        $this->assertSame(MailingRecipient::STATUS_OPENED, $this->mailingRecipient->getStatus());
        $this->assertSame(1, $this->mailingRecipient->getMailOpenCounter());
    }

    public function testPostPersistWithClickEvent()
    {
        $emailEvent = $this->createEmailEvent(EmailEvent::EVENT_CLICK);
        $this->assertSame(0, $this->mailingRecipient->getMailClickCounter());
        $this->callSubscriber($emailEvent);

        $this->assertSame(MailingRecipient::STATUS_OPENED, $this->mailingRecipient->getStatus());
        $this->assertSame(1, $this->mailingRecipient->getMailClickCounter());
    }

    public function testPostPersistWithSoftBounceEvent()
    {
        $emailEvent = $this->createEmailEvent(EmailEvent::EVENT_SOFT_BOUNCE);
        $this->optManager->cleanForHardBounce($this->contactsGroupContact)->shouldNotBeCalled();
        $this->callSubscriber($emailEvent);

        $this->assertSame(MailingRecipient::STATUS_NOT_DELIVERED, $this->mailingRecipient->getStatus());
    }

    public function testPostPersistWithRejectEvent()
    {
        $emailEvent = $this->createEmailEvent(EmailEvent::EVENT_REJECT);
        $this->callSubscriber($emailEvent);

        $this->assertSame(MailingRecipient::STATUS_NOT_DELIVERED, $this->mailingRecipient->getStatus());
    }

    public function testPostPersistWithSpamEvent()
    {
        $emailEvent = $this->createEmailEvent(EmailEvent::EVENT_SPAM);
        $this->optManager->cleanForSpam($this->contactsGroupContact)->shouldBeCalled();
        $this->callSubscriber($emailEvent);

        $this->assertSame(MailingRecipient::STATUS_SENT, $this->mailingRecipient->getStatus());
    }

    public function testPostPersistWithHardBounceEvent()
    {
        $emailEvent = $this->createEmailEvent(EmailEvent::EVENT_HARD_BOUNCE);
        $this->optManager->cleanForHardBounce($this->contactsGroupContact)->shouldBeCalled();
        $this->callSubscriber($emailEvent);

        $this->assertSame(MailingRecipient::STATUS_NOT_DELIVERED, $this->mailingRecipient->getStatus());
    }

    private function createEmailEvent(string $event): EmailEvent
    {
        $emailEvent = new EmailEvent();
        $emailEvent->setEvent($event);
        $emailEvent->setEmail($this->email);

        return $emailEvent;
    }

    private function callSubscriber(EmailEvent $emailEvent)
    {
        $this->manager->flush()->shouldBeCalled();
        $eventArgs = new LifecycleEventArgs($emailEvent, $this->manager->reveal());
        $subscriber = new EmailEventMailingRecipientUpdaterEventSubscriber($this->optManager->reveal());
        $subscriber->postPersist($eventArgs);
    }
}
