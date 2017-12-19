<?php

/*
 * This file is part of the AudienceHero project.
 *
 * (c) Marc Weistroff <marc@weistroff.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AudienceHero\Bundle\AcquisitionFreeDownloadBundle\Tests\Bridge\AudienceHero\ActivityBundle\EventListener;

use AudienceHero\Bundle\AcquisitionFreeDownloadBundle\Bridge\AudienceHero\ActivityBundle\EventListener\ActivityEventSubscriber;
use AudienceHero\Bundle\AcquisitionFreeDownloadBundle\Entity\AcquisitionFreeDownload;
use AudienceHero\Bundle\AcquisitionFreeDownloadBundle\Events\AcquisitionFreeDownloadEvent;
use AudienceHero\Bundle\AcquisitionFreeDownloadBundle\Events\AcquisitionFreeDownloadEvents;
use AudienceHero\Bundle\ActivityBundle\Builder\ActivityBuilder;
use AudienceHero\Bundle\ActivityBundle\Entity\Activity;
use AudienceHero\Bundle\ContactBundle\Entity\Contact;
use AudienceHero\Bundle\ContactBundle\Entity\ContactsGroupContact;
use AudienceHero\Bundle\CoreBundle\Entity\Person;
use AudienceHero\Bundle\CoreBundle\Entity\User;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;

class ActivityEventSubscriberTest extends TestCase
{
    /** @var ObjectProphecy */
    private $builder;
    /** @var Person */
    private $owner;
    /** @var AcquisitionFreeDownload */
    private $afd;

    public function setUp()
    {
        $this->builder = $this->prophesize(ActivityBuilder::class);
        $this->owner = new User();
        $this->afd = new AcquisitionFreeDownload();
        $this->afd->setOwner($this->owner);
    }

    private function getInstance(): ActivityEventSubscriber
    {
        return new ActivityEventSubscriber($this->builder->reveal());
    }

    public function testGetSubscribedEvents()
    {
        $this->assertSame([
              AcquisitionFreeDownloadEvents::HIT => 'onHit',
              AcquisitionFreeDownloadEvents::UNLOCK => 'onUnlock',
          ],
          ActivityEventSubscriber::getSubscribedEvents()
        );
    }

    public function testOnHit()
    {
        $this->builder->build(Argument::that(function (\DateTime $date) {
            return $date->format('r') == (new \DateTime)->format('r');
        }), $this->owner, AcquisitionFreeDownloadEvents::HIT, $this->afd)->shouldBeCalled();


        $event = AcquisitionFreeDownloadEvent::create()->setAcquisitionFreeDownload($this->afd);
        $this->getInstance()->onHit($event);
    }

    public function testOnUnlock()
    {
        $contact = new Contact();
        $contact->setOwner($this->owner);

        $activity = $this->prophesize(Activity::class);
        $activity->addSubject($contact)->shouldBeCalled();
        $this->builder->build(Argument::that(function (\DateTime $date) {
            return $date->format('r') == (new \DateTime)->format('r');
        }), $this->owner, AcquisitionFreeDownloadEvents::UNLOCK, $this->afd)->shouldBeCalled()
        ->willReturn($activity->reveal());


        $cgc = new ContactsGroupContact();
        $cgc->setContact($contact);
        $event = AcquisitionFreeDownloadEvent::create()
            ->setAcquisitionFreeDownload($this->afd)
            ->setContactsGroupContact($cgc);
        $this->getInstance()->onUnlock($event);
    }
}
