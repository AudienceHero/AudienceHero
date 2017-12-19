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

namespace AudienceHero\Bundle\AcquisitionFreeDownloadBundle\Manager;
use AudienceHero\Bundle\AcquisitionFreeDownloadBundle\Entity\AcquisitionFreeDownload;
use AudienceHero\Bundle\AcquisitionFreeDownloadBundle\Events\AcquisitionFreeDownloadEvent;
use AudienceHero\Bundle\AcquisitionFreeDownloadBundle\Events\AcquisitionFreeDownloadEvents;
use AudienceHero\Bundle\ContactBundle\Entity\Contact;
use AudienceHero\Bundle\ContactBundle\Manager\ContactManager;
use AudienceHero\Bundle\ContactBundle\Manager\OptManager;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * UnlockManager
 * @author Marc Weistroff <marc@weistroff.net>
 */
class UnlockManager
{
    /**
     * @var ContactManager
     */
    private $contactManager;
    /**
     * @var OptManager
     */
    private $optManager;
    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    public function __construct(ContactManager $contactManager, OptManager $optManager, EventDispatcherInterface $eventDispatcher)
    {
        $this->contactManager = $contactManager;
        $this->optManager = $optManager;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function unlock(AcquisitionFreeDownload $acquisitionFreeDownload, Contact $contact): void
    {
        $contact = $this->contactManager->add($contact);
        $cgc = $this->contactManager->addToGroup($contact, $acquisitionFreeDownload->getContactsGroupForm()->getContactsGroup());
        $this->optManager->dispatchOptInRequest($cgc, $acquisitionFreeDownload->getContactsGroupForm());

        $this->eventDispatcher->dispatch(
            AcquisitionFreeDownloadEvents::UNLOCK,
            AcquisitionFreeDownloadEvent::create()
                ->setContactsGroupContact($cgc)
                ->setAcquisitionFreeDownload($acquisitionFreeDownload)
        );
    }
}