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

namespace AudienceHero\Bundle\FileBundle\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use AudienceHero\Bundle\CoreBundle\Behavior\Identifiable\IdentifiableEntity;
use AudienceHero\Bundle\CoreBundle\Behavior\Identifiable\IdentifiableInterface;
use AudienceHero\Bundle\CoreBundle\Behavior\Ownable\OwnableEntity;
use AudienceHero\Bundle\CoreBundle\Behavior\Ownable\OwnableInterface;
use AudienceHero\Bundle\CoreBundle\Entity\Person;
use AudienceHero\Bundle\CoreBundle\Behavior\Referenceable\ReferenceableEntity;
use AudienceHero\Bundle\CoreBundle\Behavior\Referenceable\ReferenceableInterface;
use AudienceHero\Bundle\CoreBundle\Behavior\Timestampable\TimestampableEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="")
 * @ORM\Table(name="ah_player")
 * @ApiResource(
 *     attributes={
 *         "normalization_context"={"groups"={"read"}, "enable_max_depth"=true},
 *         "denormalization_context"={"groups"={"write"}}
 *     },
 *     collectionOperations={
 *         "get"={"method"="GET"},
 *         "post"={"method"="POST"}
 *     }
 * )
 */
class Player implements OwnableInterface, \AudienceHero\Bundle\CoreBundle\Behavior\Identifiable\IdentifiableInterface, \AudienceHero\Bundle\CoreBundle\Behavior\Referenceable\ReferenceableInterface
{
    use OwnableEntity;
    use \AudienceHero\Bundle\CoreBundle\Behavior\Timestampable\TimestampableEntity;
    use IdentifiableEntity;
    use ReferenceableEntity;

    /**
     * @var null|string
     * @ORM\Column(type="string", length=127, nullable=false)
     * @Groups({"read", "write"})
     */
    private $title;

    /**
     * @ORM\OneToMany(targetEntity="AudienceHero\Bundle\FileBundle\Entity\PlayerTrack", mappedBy="player", cascade={"all"}, orphanRemoval=true)
     * @ORM\OrderBy({"position"="asc"})
     * @Groups({"read", "write"})
     */
    private $tracks;

    public function __construct()
    {
        $this->tracks = new ArrayCollection();
    }

    /**
     * @return PlayerTrack[]
     */
    public function getTracks(): iterable
    {
        return $this->tracks;
    }

    /**
     * @param PlayerTrack[] $tracks
     */
    public function setTracks(iterable $tracks)
    {
        $this->tracks = $tracks;

        foreach ($tracks as $track) {
            $track->setPlayer($this);
        }
    }

    public function addTrack(PlayerTrack $playerTrack)
    {
        $this->tracks->add($playerTrack);
        $playerTrack->setPlayer($this);
    }

    public function removeTrack(PlayerTrack $playerTrack)
    {
        $this->tracks->removeElement($playerTrack);
        $playerTrack->setPlayer(null);
    }

    /**
     * @return null|string
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @param null|string $title
     */
    public function setTitle(?string $title): void
    {
        $this->title = $title;
    }

    public function setOwner(Person $owner)
    {
        $this->owner = $owner;

        foreach ($this->getTracks() as $track) {
            $track->setOwner($owner);
        }
    }
}
