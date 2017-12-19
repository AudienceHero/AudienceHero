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

namespace AudienceHero\Bundle\ContactBundle\Event;

use AudienceHero\Bundle\ContactBundle\Entity\Contact;
use AudienceHero\Bundle\ContactBundle\Entity\ContactsGroup;
use AudienceHero\Bundle\ContactBundle\Entity\ContactsGroupContact;
use AudienceHero\Bundle\ContactBundle\Entity\ContactsGroupForm;
use Symfony\Component\EventDispatcher\Event;

/**
 * ContactEvent.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
class ContactEvent extends Event
{
    /** @var null|Contact */
    private $contact;
    /** @var null|ContactsGroup */
    private $contactsGroup;
    /** @var null|ContactsGroupForm */
    private $contactsGroupForm;
    /** @var null|ContactsGroupContact */
    private $contactsGroupContact;

    public static function create(): ContactEvent
    {
        return new static();
    }

    /**
     * @param Contact $contact
     *
     * @return ContactEvent
     */
    public function setContact(Contact $contact): ContactEvent
    {
        $this->contact = $contact;

        return $this;
    }

    /**
     * @return Contact
     */
    public function getContact(): ?Contact
    {
        return $this->contact;
    }

    /**
     * @param ContactsGroup|null $contactsGroup
     *
     * @return ContactEvent
     */
    public function setContactsGroup(ContactsGroup $contactsGroup): ContactEvent
    {
        $this->contactsGroup = $contactsGroup;

        return $this;
    }

    /**
     * @param ContactsGroupForm|null $contactsGroupForm
     *
     * @return ContactEvent
     */
    public function setContactsGroupForm(ContactsGroupForm $contactsGroupForm): ContactEvent
    {
        $this->contactsGroupForm = $contactsGroupForm;

        return $this;
    }

    /**
     * @return ContactsGroup|null
     */
    public function getContactsGroup(): ?ContactsGroup
    {
        return $this->contactsGroup;
    }

    /**
     * @return ContactsGroupForm|null
     */
    public function getContactsGroupForm(): ?ContactsGroupForm
    {
        return $this->contactsGroupForm;
    }

    /**
     * @param ContactsGroupContact $contactsGroupContact
     *
     * @return ContactEvent
     */
    public function setContactsGroupContact(ContactsGroupContact $contactsGroupContact): ContactEvent
    {
        $this->contactsGroupContact = $contactsGroupContact;

        return $this;
    }

    /**
     * @return ContactsGroupContact
     */
    public function getContactsGroupContact(): ?ContactsGroupContact
    {
        return $this->contactsGroupContact;
    }
}
