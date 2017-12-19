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

namespace AudienceHero\Bundle\CoreBundle\Behavior\Metadata;

/**
 * HasPublicMetadata describes a class that have private metadata.
 * These metadata can be disclosed to an anonymous user.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
interface HasPublicMetadataInterface
{
    public function setPublicMetadata(array $metadata): void;

    public function getPublicMetadata(): array;

    public function setPublicMetadataValue(string $key, $value): void;

    public function getPublicMetadataValue(string $key);
}
