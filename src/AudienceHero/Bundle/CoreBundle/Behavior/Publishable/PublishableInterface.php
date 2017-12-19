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

namespace AudienceHero\Bundle\CoreBundle\Behavior\Publishable;

interface PublishableInterface
{
    const PRIVACY_PRIVATE = 'private';
    const PRIVACY_UNLISTED = 'unlisted';
    const PRIVACY_PUBLIC = 'public';
    const PRIVACY_SCHEDULED = 'scheduled';

    public function setPrivacy(string $privacy): void;

    public function getPrivacy(): ?string;

    public function setScheduledAt(?\DateTime $scheduledAt);

    public function getScheduledAt(): ?\DateTime;

    public function isPrivacyPrivate(): bool;

    public function isPrivacyUnlisted(): bool;

    public function isPrivacyPublic(): bool;

    public function isPrivacyScheduled(): bool;

    public function isTimeForPublication(): bool;

    public function publish(): void;
}
