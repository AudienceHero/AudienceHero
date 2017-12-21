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

namespace AudienceHero\Bundle\PodcastBundle\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use AudienceHero\Bundle\CoreBundle\Behavior\Identifiable\IdentifiableEntity;
use AudienceHero\Bundle\CoreBundle\Behavior\Identifiable\IdentifiableInterface;
use AudienceHero\Bundle\CoreBundle\Behavior\Linkable\LinkableEntity;
use AudienceHero\Bundle\CoreBundle\Behavior\Linkable\LinkableInterface;
use AudienceHero\Bundle\CoreBundle\Behavior\Ownable\OwnableEntity;
use AudienceHero\Bundle\CoreBundle\Behavior\Ownable\OwnableInterface;
use AudienceHero\Bundle\CoreBundle\Behavior\Publishable\PublishableEntity;
use AudienceHero\Bundle\CoreBundle\Behavior\Publishable\PublishableInterface;
use AudienceHero\Bundle\CoreBundle\Behavior\Searchable\SearchableEntity;
use AudienceHero\Bundle\CoreBundle\Behavior\Timestampable\TimestampableEntity;
use AudienceHero\Bundle\FileBundle\Entity\File;
use AudienceHero\Bundle\FileBundle\Entity\Player;
use AudienceHero\Bundle\FileBundle\Entity\PlayerTrack;
use AudienceHero\Bundle\FileBundle\Util\DurationConverter;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\MaxDepth;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * PodcastEpisode.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 *
 * @ORM\Entity(repositoryClass="AudienceHero\Bundle\PodcastBundle\Repository\PodcastEpisodeRepository")
 * @ORM\Table(name="ah_podcast_episode", indexes={@ORM\Index(columns={"slug"})})
 * @ApiResource(
 *     attributes={
 *         "filters"={"audience_hero.api.order.timestampable"},
 *         "normalization_context"={"groups"={"read"}, "enable_max_depth"=true},
 *         "denormalization_context"={"groups"={"write"}}
 *     },
 * )
 */
class PodcastEpisode implements OwnableInterface, PublishableInterface, LinkableInterface, IdentifiableInterface
{
    use TimestampableEntity;
    use PublishableEntity;
    use SearchableEntity;
    use OwnableEntity;
    use LinkableEntity;
    use IdentifiableEntity;

    /**
     * @var null|PodcastChannel
     * @ORM\ManyToOne(targetEntity="PodcastChannel", inversedBy="episodes")
     * @ORM\JoinColumn(name="podcast_channel_id", referencedColumnName="id", onDelete="CASCADE", nullable=false)
     * @Assert\NotNull
     * @MaxDepth(1)
     * @Groups({"read", "write"})
     */
    private $channel;

    /**
     * @var null|string
     * @ORM\Column(type="string", length=255, nullable=false)
     * @Assert\Length(max=255)
     * @Groups({"read", "write"})
     * @Assert\NotBlank
     */
    private $title;

    /**
     * @var null|string
     * @ORM\Column(type="string", length=255, nullable=false)
     * @Gedmo\Slug(unique=true, unique_base="channel", updatable=true, separator="-", fields={"title"})
     * @Groups({"read"})
     */
    private $slug;

    /**
     * @var null|string
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(max=255)
     * @Groups({"read", "write"})
     */
    private $subtitle;

    /**
     * @var null|string
     * @ORM\Column(type="string", length=4000, nullable=true)
     * @Assert\Length(max=4000)
     * @Groups({"read", "write"})
     */
    private $description;

    /**
     * @var null|string
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"read", "write"})
     * @Assert\Length(max=255)
     */
    private $author;

    /**
     * @var bool
     * @ORM\Column(type="boolean", nullable=false)
     * @Groups({"read", "write"})
     */
    private $itunesBlock = false;

    /**
     * @var bool
     * @ORM\Column(type="boolean", nullable=false)
     * @Groups({"read", "write"})
     */
    private $isExplicit = false;

    /**
     * @var null|File
     * @ORM\ManyToOne(targetEntity="AudienceHero\Bundle\FileBundle\Entity\File")
     * @ORM\JoinColumn(name="artwork_id", referencedColumnName="id", onDelete="SET NULL", nullable=true)
     * @Groups({"read", "write"})
     */
    private $artwork;

    /**
     * @var null|string
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"read", "write"})
     */
    private $duration;

    /**
     * @var null|File
     * @ORM\ManyToOne(targetEntity="AudienceHero\Bundle\FileBundle\Entity\File")
     * @ORM\JoinColumn(name="file_id", referencedColumnName="id", onDelete="CASCADE", nullable=false)
     * @Assert\NotNull
     * @Groups({"read", "write"})
     */
    private $file;

    /**
     * @var null|\DateTime
     * @ORM\Column(type="datetime", nullable=true)
     * @Groups({"read", "write"})
     */
    private $publishedAt;

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setChannel(PodcastChannel $channel): void
    {
        $this->channel = $channel;
        if (!$this->getOwner()) {
            $this->setOwner($this->channel->getOwner());
        }
    }

    public function getChannel(): ?PodcastChannel
    {
        return $this->channel;
    }

    public function setFile(File $file): void
    {
        $this->file = $file;
        $this->setDuration(DurationConverter::toHumanReadable($file->getPublicMetadataValue('duration')));
    }

    public function getFile(): ?File
    {
        return $this->file;
    }

    public function setAuthor(string $author): void
    {
        $this->author = $author;
    }

    public function getAuthor(): ?string
    {
        return $this->author;
    }

    public function setDuration(string $duration): void
    {
        $this->duration = $duration;
    }

    public function getDuration(): ?string
    {
        return $this->duration;
    }

    public function setIsExplicit(bool $isExplicit): void
    {
        $this->isExplicit = $isExplicit;
    }

    public function isExplicit(): bool
    {
        return $this->isExplicit;
    }

    public function setSubtitle(string $subtitle): void
    {
        $this->subtitle = $subtitle;
    }

    public function getSubtitle(): ?string
    {
        return $this->subtitle;
    }

    public function setItunesBlock(bool $itunesBlock): void
    {
        $this->itunesBlock = $itunesBlock;
    }

    public function getItunesBlock(): bool
    {
        return $this->itunesBlock;
    }

    public function setArtwork(?File $artwork): void
    {
        $this->artwork = $artwork;
    }

    public function getArtwork(): ?File
    {
        return $this->artwork;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): void
    {
        $this->slug = $slug;
    }

    public function setPublishedAt(\DateTime $publishedAt): void
    {
        $this->publishedAt = $publishedAt;
    }

    public function getPublishedAt(): ?\DateTime
    {
        if ($this->publishedAt) {
            return $this->publishedAt;
        }

        if ($this->isPrivacyScheduled()) {
            return $this->getScheduledAt();
        }

        if ($this->isPrivacyPublic() && $this->getScheduledAt()) {
            return $this->getScheduledAt();
        }

        return $this->getCreatedAt();
    }

    /**
     * Returns a Player instance to be used by the frontend.
     *
     * @Groups({"read"})
     */
    public function getPlayer(): Player
    {
        $player = new Player();
        $player->setOwner($this->getOwner());
        $player->setTitle($this->getChannel()->getTitle());

        $track = new PlayerTrack();
        $track->setPosition(0);
        $track->setTitle($this->getTitle());
        $track->setPlayer($player);
        $track->setFile($this->getFile());
        $player->addTrack($track);

        $rc = new \ReflectionClass($player);
        $idProp = $rc->getProperty('id');
        $idProp->setAccessible(true);
        $idProp->setValue($player, $this->getId());

        $rc = new \ReflectionClass($track);
        $idProp = $rc->getProperty('id');
        $idProp->setAccessible(true);
        $idProp->setValue($track, $this->getId());

        return $player;
    }
}
