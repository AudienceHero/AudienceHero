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
 * HasPrivateMetadata describes a class that have private metadata.
 * These metadata MUST NOT be disclosed to an anonymous user.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
interface HasPrivateMetadataInterface
{
    public function setPrivateMetadata(array $metadata): void;

    public function getPrivateMetadata(): array;

    public function setPrivateMetadataValue(string $key, $value): void;

    public function getPrivateMetadataValue(string $key);
}
