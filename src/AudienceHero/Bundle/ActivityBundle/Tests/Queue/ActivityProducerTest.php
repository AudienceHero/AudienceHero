<?php

/*
 * This file is part of the AudienceHero project.
 *
 * (c) Marc Weistroff <marc@weistroff.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AudienceHero\Bundle\ActivityBundle\Tests\Queue;

use AudienceHero\Bundle\ActivityBundle\Entity\Activity;
use AudienceHero\Bundle\ActivityBundle\Queue\ActivityMessage;
use AudienceHero\Bundle\ActivityBundle\Queue\ActivityProducer;
use AudienceHero\Bundle\CoreBundle\Queue\Producer;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;

class ActivityProducerTest extends TestCase
{
    public function testEnrich()
    {
        $activity = new Activity();

        $producer = $this->prophesize(Producer::class);
        $producer->sendCommand(
            ActivityProducer::ACTIVITY_ENRICH,
            Argument::that(function(ActivityMessage $message) use ($activity) {
                return $activity === $message->getActivity();
            })
        );
        $activityProducer = new ActivityProducer($producer->reveal());
        $activityProducer->enrich($activity);
    }

    public function testAggregate()
    {
        $activity = new Activity();

        $producer = $this->prophesize(Producer::class);
        $producer->sendCommand(
            ActivityProducer::ACTIVITY_AGGREGATE,
            Argument::that(function(ActivityMessage $message) use ($activity) {
                return $activity === $message->getActivity();
            })
        );
        $activityProducer = new ActivityProducer($producer->reveal());
        $activityProducer->aggregate($activity);
    }
}
