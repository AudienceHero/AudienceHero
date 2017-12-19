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

namespace AudienceHero\Bundle\ActivityBundle\Tests\Event;

use AudienceHero\Bundle\ActivityBundle\Entity\Activity;
use AudienceHero\Bundle\ActivityBundle\Event\ActivityEvent;
use PHPUnit\Framework\TestCase;

class ActivityEventTest extends TestCase
{
    public function testEvent()
    {
        $activity = new Activity();
        $event = new ActivityEvent($activity);
        $this->assertSame($activity, $event->getActivity());
    }
}
