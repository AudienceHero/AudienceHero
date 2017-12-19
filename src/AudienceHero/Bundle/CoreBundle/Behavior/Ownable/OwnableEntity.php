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

namespace AudienceHero\Bundle\CoreBundle\Behavior\Ownable;

use AudienceHero\Bundle\CoreBundle\Entity\Person;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\MaxDepth;
use Symfony\Component\Validator\Constraints as Assert;

trait OwnableEntity
{
    /**
     * @var null|Person
     * @ORM\ManyToOne(targetEntity="AudienceHero\Bundle\CoreBundle\Entity\Person")
     * @ORM\JoinColumn(name="owner_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     * @MaxDepth(1)
     * @Assert\NotNull()
     * @Groups({"private_read"})
     */
    protected $owner;

    public function setOwner(Person $owner): void
    {
        $this->owner = $owner;
    }

    public function getOwner(): ?Person
    {
        return $this->owner;
    }
}
