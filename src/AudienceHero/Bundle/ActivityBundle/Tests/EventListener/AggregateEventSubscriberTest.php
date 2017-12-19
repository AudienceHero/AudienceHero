<?php

/*
 * This file is part of the AudienceHero project.
 *
 * (c) Marc Weistroff <marc@weistroff.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AudienceHero\Bundle\ActivityBundle\Tests\EventListener;

use AudienceHero\Bundle\ActivityBundle\Entity\Activity;
use AudienceHero\Bundle\ActivityBundle\Event\ActivityEvent;
use AudienceHero\Bundle\ActivityBundle\Event\ActivityEvents;
use AudienceHero\Bundle\ActivityBundle\EventListener\AggregateEventSubscriber;
use AudienceHero\Bundle\ActivityBundle\Queue\ActivityProducer;
use PHPUnit\Framework\TestCase;

class AggregateEventSubscriberTest extends TestCase
{
    public function testSubscribedEvents()
    {
        $this->assertSame(
            [
                ActivityEvents::POST_ENRICH => 'onActivityPostEnrich',
            ],
            AggregateEventSubscriber::getSubscribedEvents()
        );
    }

    public function testOnActivityPostEnrich()
    {
        $activity = new Activity();
        $producer = $this->prophesize(ActivityProducer::class);
        $producer->aggregate($activity)->shouldBeCalled();

        $subscriber = new AggregateEventSubscriber($producer->reveal());
        $subscriber->onActivityPostEnrich(new ActivityEvent($activity));
    }
}
