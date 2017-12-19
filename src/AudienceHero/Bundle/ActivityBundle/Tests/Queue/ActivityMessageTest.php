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
use PHPUnit\Framework\TestCase;

class ActivityMessageTest extends TestCase
{
    public function testAccessors()
    {
        $activity = new Activity();
        $message = ActivityMessage::create();
        $this->assertInstanceOf(ActivityMessage::class, $message);
        $this->assertSame($message, $message->setActivity($activity));
        $this->assertSame($activity, $message->getActivity());
    }
}
