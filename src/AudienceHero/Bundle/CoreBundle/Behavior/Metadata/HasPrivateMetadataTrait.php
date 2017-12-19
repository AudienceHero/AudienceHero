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
 * HasPrivateMetadataTrait.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
trait HasPrivateMetadataTrait
{
    /**
     * @ORM\Column(type="json_document", options={"jsonb": true}, nullable=false)
     * @Groups({"private_read", "write"})
     */
    protected $privateMetadata = [];

    public function setPrivateMetadata(array $metadata): void
    {
        $this->privateMetadata = $metadata;
    }

    public function getPrivateMetadata(): array
    {
        return $this->privateMetadata;
    }

    public function setPrivateMetadataValue(string $key, $value): void
    {
        $this->privateMetadata[$key] = $value;
    }

    public function getPrivateMetadataValue(string $key)
    {
        return $this->privateMetadata[$key] ?? null;
    }
}
