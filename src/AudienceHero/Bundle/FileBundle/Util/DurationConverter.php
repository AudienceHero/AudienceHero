<?php

namespace AudienceHero\Bundle\FileBundle\Util;

/*
 * This file is part of the AudienceHero project.
 *
 * (c) Marc Weistroff <marc@weistroff.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * DurationConverter
 * @author Marc Weistroff <marc@weistroff.net>
 */
class DurationConverter
{
    public static function toHumanReadable($t): string
    {
        $hours = ($t / 3600);
        $minutes = ($t / 60 % 60);
        $seconds = $t % 60;

        $time = sprintf('%02d:%02d', $minutes, $seconds);

        if ($hours >= 1) {
            $time = sprintf('%02d:%s', $hours, $time);
        }

        return $time;
    }
}