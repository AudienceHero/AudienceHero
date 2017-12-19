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

use Symfony\Component\Serializer\Annotation\Groups;

/**
 * LocatableEntity.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
trait LinkableEntity
{
    /**
     * @Groups({"read"})
     */
    private $urls = [];

    public function setURL(string $key, string $url): void
    {
        $this->urls[$key] = $url;
    }

    public function getURL(string $key): ?string
    {
        if (isset($this->urls[$key])) {
            return $this->urls[$key];
        }

        return null;
    }

    public function getURLs(): array
    {
        return $this->urls;
    }

    public function setURLs(array $urls): void
    {
        $this->urls = $urls;
    }
}
