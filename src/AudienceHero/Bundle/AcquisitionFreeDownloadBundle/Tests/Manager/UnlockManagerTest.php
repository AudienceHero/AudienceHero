<?php

/*
 * This file is part of the AudienceHero project.
 *
 * (c) Marc Weistroff <marc@weistroff.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AudienceHero\Bundle\AcquisitionFreeDownloadBundle\Tests\Manager;

use AudienceHero\Bundle\AcquisitionFreeDownloadBundle\Entity\AcquisitionFreeDownload;
use AudienceHero\Bundle\AcquisitionFreeDownloadBundle\Events\AcquisitionFreeDownloadEvent;
use AudienceHero\Bundle\AcquisitionFreeDownloadBundle\Events\AcquisitionFreeDownloadEvents;
use AudienceHero\Bundle\AcquisitionFreeDownloadBundle\Manager\UnlockManager;
use AudienceHero\Bundle\ContactBundle\Entity\Contact;
use AudienceHero\Bundle\ContactBundle\Entity\ContactsGroup;
use AudienceHero\Bundle\ContactBundle\Entity\ContactsGroupContact;
use AudienceHero\Bundle\ContactBundle\Entity\ContactsGroupForm;
use AudienceHero\Bundle\ContactBundle\Manager\ContactManager;
use AudienceHero\Bundle\ContactBundle\Manager\OptManager;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class UnlockManagerTest extends TestCase
{
    public function testUnlock()
    {
        $afd = new AcquisitionFreeDownload();
        $contact = new Contact();
        $cg = new ContactsGroup();
        $cgf = new ContactsGroupForm();
        $cgf->setContactsGroup($cg);
        $afd->setContactsGroupForm($cgf);

        $cgc = new ContactsGroupContact();

        $contactManager = $this->prophesize(ContactManager::class);
        $contactManager->add($contact)->shouldBeCalled()->willReturn($contact);
        $contactManager->addToGroup($contact, $cg)->shouldBeCalled()->willReturn($cgc);

        $optManager = $this->prophesize(OptManager::class);
        $optManager->dispatchOptInRequest($cgc, $cgf)->shouldBeCalled();

        $eventDispatcher = $this->prophesize(EventDispatcherInterface::class);
        $eventDispatcher->dispatch(
            AcquisitionFreeDownloadEvents::UNLOCK,
            Argument::that(function(AcquisitionFreeDownloadEvent $event) use($cgc, $afd) {
                return $event->getContactsGroupContact() === $cgc && $event->getAcquisitionFreeDownload() === $afd;
            })
        );

        $unlockManager = new UnlockManager($contactManager->reveal(), $optManager->reveal(), $eventDispatcher->reveal());
        $unlockManager->unlock($afd, $contact);
    }
}
