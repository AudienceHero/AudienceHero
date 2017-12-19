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
use AudienceHero\Bundle\CoreBundle\Queue\Message;

/**
 * ActivityMessage.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
class ActivityMessage extends Message
{
    /**
     * @var Activity
     */
    private $activity;

    /**
     * @return Activity
     */
    public function getActivity(): ?Activity
    {
        return $this->activity;
    }

    /**
     * @param Activity $activity
     */
    public function setActivity(Activity $activity)
    {
        $this->activity = $activity;

        return $this;
    }
}
