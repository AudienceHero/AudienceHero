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

use AudienceHero\Bundle\CoreBundle\Behavior\Publishable\PublishableInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

trait PublishableEntity
{
    /**
     * @var null|string
     * @ORM\Column(type="string", nullable=false)
     * @Assert\NotBlank()
     * @Assert\Choice(callback="getPrivacyChoices")
     * @Groups({"read", "write"})
     */
    private $privacy;

    /**
     * @var null|\DateTime
     * @ORM\Column(type="datetime", nullable=true)
     * @Assert\GreaterThan("now")
     * @Groups({"read", "write"})
     */
    private $scheduledAt;

    public function setPrivacy(string $privacy): void
    {
        $this->privacy = $privacy;
    }

    public static function getPrivacyChoices(): array
    {
        return [
            PublishableInterface::PRIVACY_PRIVATE,
            PublishableInterface::PRIVACY_UNLISTED,
            PublishableInterface::PRIVACY_PUBLIC,
            PublishableInterface::PRIVACY_SCHEDULED,
        ];
    }

    public function getPrivacy(): ?string
    {
        return $this->privacy;
    }

    public function setScheduledAt(?\DateTime $scheduledAt): void
    {
        $this->scheduledAt = $scheduledAt;
    }

    public function getScheduledAt(): ?\DateTime
    {
        return $this->scheduledAt;
    }

    public function isPrivacyPrivate(): bool
    {
        return PublishableInterface::PRIVACY_PRIVATE === $this->getPrivacy();
    }

    public function isPrivacyUnlisted(): bool
    {
        return PublishableInterface::PRIVACY_UNLISTED === $this->getPrivacy();
    }

    public function isPrivacyPublic(): bool
    {
        return PublishableInterface::PRIVACY_PUBLIC === $this->getPrivacy();
    }

    public function isPrivacyScheduled(): bool
    {
        return PublishableInterface::PRIVACY_SCHEDULED === $this->getPrivacy();
    }

    public function isTimeForPublication(): bool
    {
        if (!$this->isPrivacyScheduled()) {
            return false;
        }

        if (!$this->getScheduledAt()) {
            return true;
        }

        $now = new \DateTime();

        return $now > $this->getScheduledAt();
    }

    public function publish(): void
    {
        $this->setPrivacy(PublishableInterface::PRIVACY_PUBLIC);
    }
}
