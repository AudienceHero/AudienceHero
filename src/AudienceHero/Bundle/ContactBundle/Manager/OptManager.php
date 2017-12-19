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

namespace AudienceHero\Bundle\ContactBundle\Manager;

use AudienceHero\Bundle\ContactBundle\Entity\ContactsGroupContact;
use AudienceHero\Bundle\ContactBundle\Entity\ContactsGroupForm;
use AudienceHero\Bundle\ContactBundle\Event\ContactEvent;
use AudienceHero\Bundle\ContactBundle\Event\ContactEvents;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * OptOutManager.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
class OptManager
{
    /**
     * @var RegistryInterface
     */
    private $registry;
    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    public function optin(ContactsGroupContact $contactsGroupContact)
    {
        if (!$contactsGroupContact->isOptin()) {
            $contactsGroupContact->resubscribe();
            $contactsGroupContact->removeCleanState();
            $contactsGroupContact->setOptinAt(new \DateTime());
        }
    }

    public function dispatchOptInRequest(ContactsGroupContact $contactsGroupContact, ContactsGroupForm $contactsGroupForm)
    {
        $this->eventDispatcher->dispatch(
            ContactEvents::OPT_IN_REQUEST,
            ContactEvent::create()
            ->setContact($contactsGroupContact->getContact())
            ->setContactsGroupContact($contactsGroupContact)
            ->setContactsGroup($contactsGroupContact->getGroup())
            ->setContactsGroupForm($contactsGroupForm)
        );
    }

    public function dispatchOptInConfirmed(ContactsGroupContact $contactsGroupContact, ContactsGroupForm $contactsGroupForm)
    {
        $this->eventDispatcher->dispatch(
            ContactEvents::OPT_IN_CONFIRMED,
            ContactEvent::create()
                ->setContact($contactsGroupContact->getContact())
                ->setContactsGroupForm($contactsGroupForm)
                ->setContactsGroupContact($contactsGroupContact)
                ->setContactsGroup($contactsGroupContact->getGroup())
        );
    }

    public function optout(ContactsGroupContact $contactsGroupContact)
    {
        $contactsGroupContact->unsubscribe();
        $this->eventDispatcher->dispatch(
            ContactEvents::OPT_OUT,
            ContactEvent::create()
                ->setContactsGroupContact($contactsGroupContact)
                ->setContact($contactsGroupContact->getContact())
                ->setContactsGroup($contactsGroupContact->getGroup())
        );
    }

    public function cleanForHardBounce(ContactsGroupContact $contactsGroupContact)
    {
        $contactsGroupContact->setCleanedAt(new \DateTime());
        $contactsGroupContact->setCleanedReason(ContactsGroupContact::CLEAN_REASON_HARD_BOUNCE);
    }

    public function cleanForSpam(ContactsGroupContact $contactsGroupContact)
    {
        $contactsGroupContact->setCleanedAt(new \DateTime());
        $contactsGroupContact->setCleanedReason(ContactsGroupContact::CLEAN_REASON_SPAM);
    }
}
