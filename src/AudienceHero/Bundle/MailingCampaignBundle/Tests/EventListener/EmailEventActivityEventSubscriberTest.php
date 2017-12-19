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

use AudienceHero\Bundle\ActivityBundle\Entity\Activity;
use AudienceHero\Bundle\CoreBundle\Entity\User;
use AudienceHero\Bundle\MailingCampaignBundle\Entity\EmailEvent;
use AudienceHero\Bundle\MailingCampaignBundle\EventListener\EmailEventActivityEventSubscriber;
use AudienceHero\Bundle\MailingCampaignBundle\Factory\EmailEventActivityFactory;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Event\LifecycleEventArgs;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;

class EmailEventActivityEventSubscriberTest extends TestCase
{
    /** @var ObjectProphecy */
    private $activityFactory;
    /** @var ObjectProphecy */
    private $manager;

    public function setUp()
    {
        $this->activityFactory = $this->prophesize(EmailEventActivityFactory::class);
        $this->manager = $this->prophesize(EntityManager::class);
    }

    public function testSubscriberDoesNothingWhenEntityIsNotAnEmailEvent()
    {
        $object = new User();

        $this->activityFactory->createFromEmailEvent(Argument::any())->shouldNotBeCalled();
        $this->manager->persist(Argument::any())->shouldNotBeCalled();
        $this->manager->flush()->shouldNotBeCalled();

        $eventArgs = new LifecycleEventArgs($object, $this->manager->reveal());

        $subscriber = new EmailEventActivityEventSubscriber($this->activityFactory->reveal());
        $subscriber->postPersist($eventArgs);
    }

    public function testSubscriberCreateActivity()
    {
        $object = new EmailEvent();
        $activity = new Activity();

        $this->activityFactory->createFromEmailEvent($object)->shouldBeCalled()
             ->willReturn($activity);

        $this->manager->persist($activity)->shouldBeCalled();
        $this->manager->flush()->shouldBeCalled();

        $eventArgs = new LifecycleEventArgs($object, $this->manager->reveal());

        $subscriber = new EmailEventActivityEventSubscriber($this->activityFactory->reveal());
        $subscriber->postPersist($eventArgs);
    }

    public function testGetSubscribedEvents()
    {
        $subscriber = new EmailEventActivityEventSubscriber($this->activityFactory->reveal());
        $this->assertSame(['postPersist'], $subscriber->getSubscribedEvents());
    }
}
