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

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @author Marc Weistroff <marc@weistroff.net>
 */
trait HasPublicMetadataTrait
{
    /**
     * @ORM\Column(type="json_document", options={"jsonb": true}, nullable=false)
     * @Groups({"read", "write"})
     */
    protected $publicMetadata = [];

    public function setPublicMetadata(array $metadata): void
    {
        $this->publicMetadata = $metadata;
    }

    public function getPublicMetadata(): array
    {
        return $this->publicMetadata;
    }

    public function setPublicMetadataValue(string $key, $value): void
    {
        $this->publicMetadata[$key] = $value;
    }

    public function getPublicMetadataValue(string $key)
    {
        return $this->publicMetadata[$key] ?? null;
    }
}
