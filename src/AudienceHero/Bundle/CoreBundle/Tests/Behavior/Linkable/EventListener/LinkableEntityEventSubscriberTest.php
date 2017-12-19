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

namespace AudienceHero\Bundle\CoreBundle\Tests\Behavior\Linkable\EventListener;

use AudienceHero\Bundle\CoreBundle\Behavior\Linkable\LinkableEntity;
use AudienceHero\Bundle\CoreBundle\Behavior\Linkable\LinkableInterface;
use AudienceHero\Bundle\CoreBundle\Behavior\Linkable\EventListener\LinkableEntityEventSubscriber;
use AudienceHero\Bundle\CoreBundle\Behavior\Linkable\Populator\LinkablePopulatorChain;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;
use PHPUnit\Framework\TestCase;

class LocatableEntityEventSubscriberTest extends TestCase
{
    public function testSubscribedEvents()
    {
        $subscriber = new LinkableEntityEventSubscriber($this->prophesize(LinkablePopulatorChain::class)->reveal());
        $events = $subscriber->getSubscribedEvents();
        $this->assertSame(['postLoad'], $events);
    }

    public function testCallPopulatorOnLocatableInterface()
    {
        $object = new class() implements LinkableInterface {
            use LinkableEntity;
        };

        $populator = $this->prophesize(LinkablePopulatorChain::class);
        $populator->populate($object)->shouldBeCalled();

        $subscriber = new LinkableEntityEventSubscriber($populator->reveal());
        $args = new LifecycleEventArgs($object, $this->prophesize(EntityManagerInterface::class)->reveal());
        $subscriber->postLoad($args);
    }

    public function testNoOpOnOtherObjects()
    {
        $object = new class() {
        };

        $populator = $this->prophesize(LinkablePopulatorChain::class);
        $populator->populate($object)->shouldNotBeCalled();

        $subscriber = new LinkableEntityEventSubscriber($populator->reveal());
        $args = new LifecycleEventArgs($object, $this->prophesize(EntityManagerInterface::class)->reveal());
        $subscriber->postLoad($args);
    }
}
