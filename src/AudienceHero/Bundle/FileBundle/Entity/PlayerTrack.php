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
use AudienceHero\Bundle\CoreBundle\Behavior\Timestampable\TimestampableEntity;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @author Marc Weistroff <marc@weistroff.net>
 * @ORM\Entity(repositoryClass="Gedmo\Sortable\Entity\Repository\SortableRepository")
 * @ORM\Table(name="ah_player_track")
 * @ApiResource(
 *     attributes={
 *         "normalization_context"={"groups"={"read"}, "enable_max_depth"=true},
 *         "denormalization_context"={"groups"={"write"}}
 *     },
 *     collectionOperations={
 *        "get"={"method"="GET"},
 *        "post"={"method"="POST"},
 *     },
 *     itemOperations={
 *        "get"={"method"="GET"},
 *        "put"={"method"="PUT"},
 *     },
 * )
 */
class PlayerTrack implements \AudienceHero\Bundle\CoreBundle\Behavior\Ownable\OwnableInterface, \AudienceHero\Bundle\CoreBundle\Behavior\Identifiable\IdentifiableInterface
{
    use \AudienceHero\Bundle\CoreBundle\Behavior\Timestampable\TimestampableEntity;
    use \AudienceHero\Bundle\CoreBundle\Behavior\Ownable\OwnableEntity;
    use \AudienceHero\Bundle\CoreBundle\Behavior\Identifiable\IdentifiableEntity;

    /**
     * @var null|int
     * @ORM\Column(type="integer", nullable=false)
     * @Gedmo\SortablePosition()
     * @Groups({"read", "write", "player"})
     */
    private $position;

    /**
     * @ORM\Column(type="string", length=127, nullable=false)
     * @Assert\NotBlank()
     * @Assert\Length(max=127)
     * @Groups({"read", "write", "player"})
     */
    private $title;

    /**
     * @var null|Player
     * @ORM\ManyToOne(targetEntity="Player", inversedBy="tracks")
     * @ORM\JoinColumn(name="player_id", referencedColumnName="id", onDelete="CASCADE", nullable=false)
     * @Gedmo\SortableGroup()
     */
    private $player;

    /**
     * @ORM\ManyToOne(targetEntity="File")
     * TODO: Should we really RESTRICT here?
     * @ORM\JoinColumn(name="file_id", referencedColumnName="id", onDelete="RESTRICT", nullable=false)
     * @Assert\Valid()
     * @Assert\NotNull()
     * @Groups({"read", "write", "player"})
     */
    private $file;

    public function setId(string $id): void
    {
        $this->id = $id;
    }

    public function __toString(): string
    {
        return sprintf('%s - %s', $this->position + 1, $this->title);
    }

    public function setPosition(int $position): void
    {
        $this->position = $position;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setFile(File $file): void
    {
        $this->file = $file;
    }

    public function getFile(): ?File
    {
        return $this->file;
    }

    /**
     * @return Player|null
     */
    public function getPlayer(): ?Player
    {
        return $this->player;
    }

    /**
     * @param Player|null $player
     */
    public function setPlayer(?Player $player): void
    {
        $this->player = $player;
        if ($player && $player->getOwner()) {
            $this->setOwner($player->getOwner());
        }
    }
}
