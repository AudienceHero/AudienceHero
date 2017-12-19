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

namespace AudienceHero\Bundle\AcquisitionFreeDownloadBundle\Events;

use AudienceHero\Bundle\AcquisitionFreeDownloadBundle\Entity\AcquisitionFreeDownload;
use AudienceHero\Bundle\ContactBundle\Entity\ContactsGroupContact;
use Symfony\Component\EventDispatcher\Event;

/**
 * AcquisitionFreeDownloadEvent.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
final class AcquisitionFreeDownloadEvent extends Event
{
    /** @var AcquisitionFreeDownload */
    private $acquisitionFreeDownload;

    /** @var ContactsGroupContact */
    private $contactsGroupContact;

    public static function create(): AcquisitionFreeDownloadEvent
    {
        return new static();
    }

    /**
     * @return AcquisitionFreeDownload
     */
    public function getAcquisitionFreeDownload(): AcquisitionFreeDownload
    {
        return $this->acquisitionFreeDownload;
    }

    /**
     * @param AcquisitionFreeDownload $acquisitionFreeDownload
     *
     * @return AcquisitionFreeDownloadEvent
     */
    public function setAcquisitionFreeDownload(AcquisitionFreeDownload $acquisitionFreeDownload): AcquisitionFreeDownloadEvent
    {
        $this->acquisitionFreeDownload = $acquisitionFreeDownload;

        return $this;
    }

    /**
     * @return ContactsGroupContact
     */
    public function getContactsGroupContact(): ContactsGroupContact
    {
        return $this->contactsGroupContact;
    }

    /**
     * @param ContactsGroupContact $contactsGroupContact
     */
    public function setContactsGroupContact(ContactsGroupContact $contactsGroupContact): AcquisitionFreeDownloadEvent
    {
        $this->contactsGroupContact = $contactsGroupContact;

        return $this;
    }
}
