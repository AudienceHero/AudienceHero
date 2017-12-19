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

namespace AudienceHero\Bundle\ActivityBundle\Queue;

use AudienceHero\Bundle\ActivityBundle\Entity\Activity;
use AudienceHero\Bundle\CoreBundle\Queue\Producer;

/**
 * Publisher.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
class ActivityProducer
{
    const ACTIVITY_ENRICH = 'activity.enrich';
    const ACTIVITY_AGGREGATE = 'activity.aggregate';

    /**
     * @var Producer
     */
    private $producer;

    public function __construct(Producer $producer)
    {
        $this->producer = $producer;
    }

    public function enrich(Activity $activity): void
    {
        $this->producer->sendCommand(self::ACTIVITY_ENRICH, ActivityMessage::create()->setActivity($activity));
    }

    public function aggregate(Activity $activity): void
    {
        $this->producer->sendCommand(self::ACTIVITY_AGGREGATE, ActivityMessage::create()->setActivity($activity));
    }
}
