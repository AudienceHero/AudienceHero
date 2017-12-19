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

namespace AudienceHero\Bundle\CoreBundle\Behavior\Referenceable;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * ReferenceableEntity.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
trait ReferenceableEntity
{
    /**
     * @var null|string
     * @ORM\Column(type="string", length=64, nullable=true)
     * @Groups({"private_read", "write"})
     * @Assert\Length(max=128)
     */
    private $reference;

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(?string $reference): void
    {
        $this->reference = $reference;
    }
}
