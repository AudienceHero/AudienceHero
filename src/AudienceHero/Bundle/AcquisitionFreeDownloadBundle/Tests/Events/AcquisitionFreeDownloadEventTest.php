<?php

/*
 * This file is part of the AudienceHero project.
 *
 * (c) Marc Weistroff <marc@weistroff.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AudienceHero\Bundle\AcquisitionFreeDownloadBundle\Tests\Events;

use AudienceHero\Bundle\AcquisitionFreeDownloadBundle\Entity\AcquisitionFreeDownload;
use AudienceHero\Bundle\AcquisitionFreeDownloadBundle\Events\AcquisitionFreeDownloadEvent;
use AudienceHero\Bundle\ContactBundle\Entity\ContactsGroupContact;
use PHPUnit\Framework\TestCase;

class AcquisitionFreeDownloadEventTest extends TestCase
{
    public function testCreate()
    {
        $this->assertInstanceOf(AcquisitionFreeDownloadEvent::class, AcquisitionFreeDownloadEvent::create());
    }

    public function testAcquisitionFreeDownloadAccessors()
    {
        $event = AcquisitionFreeDownloadEvent::create();
        $afd = new AcquisitionFreeDownload();
        $this->assertSame($event, $event->setAcquisitionFreeDownload($afd));
        $this->assertSame($afd, $event->getAcquisitionFreeDownload());
    }

    public function testContactsGroupContactAccessors()
    {
        $event = AcquisitionFreeDownloadEvent::create();
        $cgc = new ContactsGroupContact();
        $this->assertSame($event, $event->setContactsGroupContact($cgc));
        $this->assertSame($cgc, $event->getContactsGroupContact());
    }
}
