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

namespace AudienceHero\Bundle\CoreBundle\Behavior\Linkable;

/**
 * LocatableInterface.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
interface LinkableInterface
{
    public function setURL(string $key, string $url): void;

    public function getURL(string $key): ?string;

    public function getURLs(): array;

    public function setURLs(array $urls): void;
}
