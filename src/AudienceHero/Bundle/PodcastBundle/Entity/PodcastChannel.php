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
use AudienceHero\Bundle\ContactBundle\Entity\ContactsGroupForm;
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
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\MaxDepth;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * PodcastChannel.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 * @ORM\Entity(repositoryClass="AudienceHero\Bundle\PodcastBundle\Repository\PodcastChannelRepository")
 * @ORM\Table(name="ah_podcast_channel", indexes={@ORM\Index(columns={"slug"})})
 * @ApiResource(
 *     attributes={
 *         "filters"={"audience_hero.api.order.timestampable"},
 *         "normalization_context"={"groups"={"read"}, "enable_max_depth"=true},
 *         "denormalization_context"={"groups"={"write"}}
 *     }
 * )
 */
class PodcastChannel implements OwnableInterface, PublishableInterface, LinkableInterface, IdentifiableInterface
{
    const I18N_VIOLATION_ARTWORK_TOO_SMALL = 'violation.podcast_artwork.too_small';

    use LinkableEntity;
    use OwnableEntity;
    use PublishableEntity;
    use SearchableEntity;
    use TimestampableEntity;
    use IdentifiableEntity;

    /**
     * @var null|string
     * @ORM\Column(type="string", length=255, nullable=false)
     * @Assert\Length(max=255)
     * @Assert\NotBlank
     * @Groups({"read", "write"})
     */
    private $title;

    /**
     * @var null|string
     * @ORM\Column(type="string", length=255, unique=false)
     * @Groups({"read"})
     * @Gedmo\Slug(unique=true, unique_base="owner", updatable=true, separator="-", fields={"title"})
     */
    private $slug;

    /**
     * @var null|string
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"read", "write"})
     * @Assert\Length(max=255)
     */
    private $subtitle;

    /**
     * @var null|string
     * @ORM\Column(type="string", length=4000, nullable=true)
     * @Groups({"read", "write"})
     * @Assert\Length(max=4000)
     * @Assert\NotBlank
     */
    private $description;

    /**
     * @var null|string
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(max=255)
     * @Groups({"read", "write"})
     */
    private $copyright;

    /**
     * @var Collection
     * @ORM\OneToMany(targetEntity="PodcastEpisode", mappedBy="channel")
     * @ORM\OrderBy({"createdAt"="DESC"})
     * @MaxDepth(1)
     */
    private $episodes;

    /**
     * @var null|ContactsGroupForm
     * @ORM\ManyToOne(targetEntity="AudienceHero\Bundle\ContactBundle\Entity\ContactsGroupForm")
     * @ORM\JoinColumn(name="contacts_group_form_id", referencedColumnName="id", onDelete="SET NULL", nullable=true)
     * @Groups({"read", "write"})
     * @MaxDepth(1)
     */
    private $contactsGroupForm;

    /**
     * @var null|string
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"read", "write"})
     * @Assert\Length(max=255)
     */
    private $author;

    /**
     * @var bool
     * @Groups({"read", "write"})
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $itunesBlock = false;

    /**
     * @var null|string
     * @Groups({"read", "write"})
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $category;

    /**
     * @var bool
     * @Groups({"read", "write"})
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $isExplicit = false;

    /**
     * @var bool
     * @Groups({"read", "write"})
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $isComplete = false;

    /**
     * @ORM\ManyToOne(targetEntity="AudienceHero\Bundle\FileBundle\Entity\File")
     * @ORM\JoinColumn(name="artwork_id", referencedColumnName="id", onDelete="SET NULL", nullable=true)
     *
     * @var null|File
     * @Groups({"read", "write"})
     */
    private $artwork;

    /**
     * @var null|string
     * @Groups({"read", "write"})
     * @ORM\Column(type="string", length=8, nullable=false)
     * @Assert\NotBlank()
     */
    private $language;

    /**
     * @var null|string
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(max=255)
     * @Assert\Email
     * @Groups({"read", "write"})
     */
    private $itunesOwnerEmail;

    /**
     * @var null|string
     * @Assert\Length(max=255)
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"read", "write"})
     */
    private $itunesOwnerName;

    public function __construct()
    {
        $this->episodes = new ArrayCollection();
    }

    public function setOwner(\AudienceHero\Bundle\CoreBundle\Entity\Person $owner): void
    {
        $this->owner = $owner;

        if (!$this->getItunesOwnerEmail()) {
            $this->setItunesOwnerEmail($owner->getEmail());
        }
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setSubtitle(string $subtitle): void
    {
        $this->subtitle = $subtitle;
    }

    public function getSubtitle(): ?string
    {
        return $this->subtitle;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getEpisodes(): Collection
    {
        return $this->episodes;
    }

    public function addEpisode(PodcastEpisode $episode): void
    {
        $this->episodes[] = $episode;
    }

    public function setCopyright(?string $copyright): void
    {
        $this->copyright = $copyright;
    }

    public function getCopyright(): ?string
    {
        return $this->copyright;
    }

    public function setAuthor(?string $author): void
    {
        $this->author = $author;
    }

    public function getAuthor(): ?string
    {
        return $this->author;
    }

    public function setItunesBlock(bool $itunesBlock): void
    {
        $this->itunesBlock = $itunesBlock;
    }

    public function getItunesBlock(): bool
    {
        return $this->itunesBlock;
    }

    public function setCategory(string $category): void
    {
        $this->category = $category;
    }

    public function getCategory(): ?string
    {
        return $this->category;
    }

    public function setIsExplicit(bool $isExplicit): void
    {
        $this->isExplicit = $isExplicit;
    }

    public function getIsExplicit(): bool
    {
        return $this->isExplicit;
    }

    public function setIsComplete(bool $isComplete): void
    {
        $this->isComplete = $isComplete;
    }

    public function getIsComplete(): bool
    {
        return $this->isComplete;
    }

    public function setItunesOwnerEmail(?string $itunesOwnerEmail): void
    {
        $this->itunesOwnerEmail = $itunesOwnerEmail;
    }

    public function getItunesOwnerEmail(): ?string
    {
        return $this->itunesOwnerEmail;
    }

    public function setItunesOwnerName(?string $itunesOwnerName): void
    {
        $this->itunesOwnerName = $itunesOwnerName;
    }

    public function getItunesOwnerName(): ?string
    {
        return $this->itunesOwnerName;
    }

    /**
     * @Assert\Callback
     */
    public function validate(ExecutionContextInterface $context)
    {
        if ($artwork = $this->getArtwork()) {
//            if ($artwork->getImageWidth() < 1400 || $artwork->getImageHeight() < 1400) {
//                $context->buildViolation(self::I18N_VIOLATION_ARTWORK_TOO_SMALL)
//                    ->atPath('artwork')
//                    ->addViolation();
//            }
        }
    }

    public function setArtwork(?File $artwork): void
    {
        $this->artwork = $artwork;
    }

    public function getArtwork(): ?File
    {
        return $this->artwork;
    }

    public function setLanguage(string $language): void
    {
        $this->language = $language;
    }

    public function getLanguage(): ?string
    {
        return $this->language;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): void
    {
        $this->slug = $slug;
    }

    /**
     * @Groups({"read"})
     */
    public function getPublicEpisodes(): Collection
    {
        return $this->getEpisodes()->filter(function (PodcastEpisode $episode) {
            return $episode->isPrivacyPublic();
        });
    }

    /**
     * @return ContactsGroupForm|null
     */
    public function getContactsGroupForm(): ?ContactsGroupForm
    {
        return $this->contactsGroupForm;
    }

    /**
     * @param ContactsGroupForm|null $contactsGroupForm
     */
    public function setContactsGroupForm(?ContactsGroupForm $contactsGroupForm): void
    {
        $this->contactsGroupForm = $contactsGroupForm;
    }
}
