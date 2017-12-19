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

namespace AudienceHero\Bundle\CoreBundle\Generator;

use Ramsey\Uuid\Uuid;

/**
 * UUIDGenerator.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
class UUIDGenerator
{
    /**
     * Returns an UUID.
     *
     * @return string
     */
    public function generate(): string
    {
        return Uuid::uuid4()->toString();
    }
}
