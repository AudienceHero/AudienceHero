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

namespace AudienceHero\Bundle\ActivityBundle\Event;

use AudienceHero\Bundle\ActivityBundle\Entity\Activity;
use Symfony\Component\EventDispatcher\Event;

final class ActivityEvent extends Event
{
    /**
     * @var Activity
     */
    private $activity;

    /**
     * ActivityEvent constructor.
     *
     * @param Activity $activity
     */
    public function __construct(Activity $activity)
    {
        $this->activity = $activity;
    }

    public function getActivity(): Activity
    {
        return $this->activity;
    }
}
