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

namespace AudienceHero\Bundle\AcquisitionFreeDownloadBundle\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use AudienceHero\Bundle\ContactBundle\Entity\ContactsGroupForm;
use AudienceHero\Bundle\CoreBundle\Behavior\Identifiable\IdentifiableEntity;
use AudienceHero\Bundle\CoreBundle\Behavior\Identifiable\IdentifiableInterface;
use AudienceHero\Bundle\CoreBundle\Behavior\Linkable\LinkableEntity;
use AudienceHero\Bundle\CoreBundle\Behavior\Linkable\LinkableInterface;
use AudienceHero\Bundle\CoreBundle\Behavior\Ownable\OwnableEntity;
use AudienceHero\Bundle\CoreBundle\Behavior\Ownable\OwnableInterface;
use AudienceHero\Bundle\CoreBundle\Entity\PersonEmail;
use AudienceHero\Bundle\CoreBundle\Behavior\Publishable\PublishableEntity;
use AudienceHero\Bundle\CoreBundle\Behavior\Publishable\PublishableInterface;
use AudienceHero\Bundle\CoreBundle\Behavior\Referenceable\ReferenceableEntity;
use AudienceHero\Bundle\CoreBundle\Behavior\Referenceable\ReferenceableInterface;
use AudienceHero\Bundle\CoreBundle\Behavior\Searchable\SearchableEntity;
use AudienceHero\Bundle\CoreBundle\Behavior\Timestampable\TimestampableEntity;
use AudienceHero\Bundle\CoreBundle\Validator\Constraints\PersonEmailVerified;
use AudienceHero\Bundle\FileBundle\Entity\File;
use AudienceHero\Bundle\FileBundle\Entity\Player;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * AcquisitionFreeDownload.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 *
 * @ORM\Entity(repositoryClass="AudienceHero\Bundle\AcquisitionFreeDownloadBundle\Repository\AcquisitionFreeDownloadRepository")
 * @ORM\Table(name="ah_acquisition_free_download", indexes={@ORM\Index(columns={"slug"})})
 * @ApiResource(
 *     attributes={
 *         "normalization_context"={"groups"={"read"}},
 *         "denormalization_context"={"groups"={"write"}},
 *     },
 *     collectionOperations={
 *         "get"={"method"="GET"},
 *         "post"={"method"="POST"},
 *     },
 *     itemOperations={
 *         "get"={"method"="GET"},
 *         "put"={"method"="PUT"},
 *         "unlock"={"route_name"="api_acquisition_free_downloads_unlock"}
 *     }
 * )
 */
class AcquisitionFreeDownload implements OwnableInterface, PublishableInterface, LinkableInterface, ReferenceableInterface, IdentifiableInterface
{
    use OwnableEntity;
    use PublishableEntity;
    use SearchableEntity;
    use TimestampableEntity;
    use LinkableEntity;
    use ReferenceableEntity;
    use IdentifiableEntity;

    /**
     * @var null|string
     * @ORM\Column(type="string", length=127, nullable=false)
     * @Groups({"read", "write"})
     * @Assert\NotBlank
     * @Assert\Length(max=127)
     */
    private $title;

    /**
     * @var null|string
     * @ORM\Column(type="string", length=255, unique=false)
     * @Gedmo\Slug(unique=true, unique_base="owner", updatable=true, separator="-", fields={"title"})
     */
    private $slug;

    /**
     * @var null|string
     * @ORM\Column(type="text", nullable=true)
     * @Groups({"read", "write"})
     * @Assert\Length(max=2048)
     */
    private $description;

    /**
     * @var null|File
     * @ORM\ManyToOne(targetEntity="AudienceHero\Bundle\FileBundle\Entity\File")
     * @ORM\JoinColumn(name="artwork_id", referencedColumnName="id", onDelete="SET NULL", nullable=true)
     * @Groups({"read", "write"})
     * @Assert\NotNull
     */
    private $artwork;

    /**
     * @var null|File
     * @Groups({"read", "write"})
     * @ORM\ManyToOne(targetEntity="AudienceHero\Bundle\FileBundle\Entity\File")
     * @ORM\JoinColumn(name="download_id", referencedColumnName="id", onDelete="SET NULL", nullable=true)
     * @Assert\NotNull
     */
    private $download;

    /**
     * @ORM\ManyToOne(targetEntity="AudienceHero\Bundle\ContactBundle\Entity\ContactsGroupForm")
     * @ORM\JoinColumn(name="contacts_group_form_id", referencedColumnName="id", onDelete="RESTRICT", nullable=true)
     * @Groups({"read", "write"})
     *
     * @var null|ContactsGroupForm
     */
    private $contactsGroupForm;

    /**
     * @var null|string
     * @ORM\Column(type="string", length=255, nullable=false)
     * @Groups({"read", "write"})
     *
     * @Assert\NotBlank
     * @Assert\Length(max=255)
     */
    private $fromName;

    /**
     * @var null|PersonEmail
     * @ORM\ManyToOne(targetEntity="AudienceHero\Bundle\CoreBundle\Entity\PersonEmail")
     * @ORM\JoinColumn(name="person_email_id", referencedColumnName="id", nullable=true, onDelete="RESTRICT")
     * @Assert\NotNull()
     * @PersonEmailVerified()
     * @Groups({"read", "write"})
     */
    private $personEmail;

    /**
     * @var null|Player
     * @ORM\ManyToOne(targetEntity="AudienceHero\Bundle\FileBundle\Entity\Player")
     * @ORM\JoinColumn(name="player_id")
     * @Assert\NotNull()
     * @ORM\OrderBy({"position"="ASC"})
     * @Groups({"read", "write", "player"})
     */
    private $player;

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

    public function setArtwork(?File $artwork): void
    {
        $this->artwork = $artwork;
    }

    public function getArtwork(): ?File
    {
        return $this->artwork;
    }

    public function setDownload(?File $download): void
    {
        $this->download = $download;
    }

    public function getDownload(): ?File
    {
        return $this->download;
    }

    public function setFromName(string $fromName): void
    {
        $this->fromName = $fromName;
    }

    public function getFromName(): ?string
    {
        return $this->fromName;
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
     * @return PersonEmail|null
     */
    public function getPersonEmail(): ?PersonEmail
    {
        return $this->personEmail;
    }

    /**
     * @param PersonEmail|null $personEmail
     */
    public function setPersonEmail(?PersonEmail $personEmail): void
    {
        $this->personEmail = $personEmail;
    }

    /**
     * @return null|Player
     */
    public function getPlayer(): ?Player
    {
        return $this->player;
    }

    /**
     * @param null|Player $player
     */
    public function setPlayer(?Player $player)
    {
        $this->player = $player;
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
