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

namespace AudienceHero\Bundle\CoreBundle\Queue;

/**
 * Message.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
abstract class Message
{
    public static function create(): self
    {
        return new static();
    }
}
