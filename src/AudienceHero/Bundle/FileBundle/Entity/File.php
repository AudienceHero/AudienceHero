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
use AudienceHero\Bundle\CoreBundle\Behavior\Metadata\HasPrivateMetadataInterface;
use AudienceHero\Bundle\CoreBundle\Behavior\Metadata\HasPrivateMetadataTrait;
use AudienceHero\Bundle\CoreBundle\Behavior\Metadata\HasPublicMetadataInterface;
use AudienceHero\Bundle\CoreBundle\Behavior\Metadata\HasPublicMetadataTrait;
use AudienceHero\Bundle\CoreBundle\Behavior\Identifiable\IdentifiableEntity;
use AudienceHero\Bundle\CoreBundle\Behavior\Identifiable\IdentifiableInterface;
use AudienceHero\Bundle\CoreBundle\Behavior\Ownable\OwnableEntity;
use AudienceHero\Bundle\CoreBundle\Behavior\Ownable\OwnableInterface;
use AudienceHero\Bundle\CoreBundle\Behavior\Searchable\SearchableEntity;
use AudienceHero\Bundle\CoreBundle\Behavior\Timestampable\TimestampableEntity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="AudienceHero\Bundle\FileBundle\Repository\FileRepository")
 * @ORM\Table(name="ah_file")
 * @ApiResource(
 *     attributes={
 *         "filters"={"audience_hero.api.order.timestampable", "audience_hero_file.order", "audience_hero_file.search"},
 *         "normalization_context"={"groups"={"read"}},
 *         "denormalization_context"={"groups"={"write"}}
 *     },
 *     collectionOperations={
 *         "get"={"method"="GET"},
 *     },
 *     itemOperations={
 *         "get"={"method"="GET"},
 *         "put"={"method"="PUT"},
 *     }
 * )
 */
class File implements OwnableInterface, IdentifiableInterface, HasPrivateMetadataInterface, HasPublicMetadataInterface
{
    use SearchableEntity;
    use TimestampableEntity;
    use OwnableEntity;
    use IdentifiableEntity;
    use HasPrivateMetadataTrait;
    use HasPublicMetadataTrait;

    const FILETYPE_IMAGE = 'image';
    const FILETYPE_AUDIO = 'audio';
    const FILETYPE_VIDEO = 'video';
    const FILETYPE_ARCHIVE = 'archive';
    const FILETYPE_PDF = 'pdf';

    public static $allowedMimeTypes = [
        'application/octet-stream',
        'application/zip',
        'application/rar',
        'application/x-rar',
        'application/x-rar-compressed',
        'image/jpeg',
        'image/png',
        'audio/wav',
        'audio/x-wav',
        'audio/mp3',
        'audio/mpeg',
        'audio/mpeg3',
        'audio/x-mpeg-3',
    ];

    public static $filetypes = [
        self::FILETYPE_IMAGE,
        self::FILETYPE_AUDIO,
        self::FILETYPE_VIDEO,
        self::FILETYPE_ARCHIVE,
        self::FILETYPE_PDF,
    ];

    /**
     * @var null|string
     * @ORM\Column(type="string", length=128)
     * @Assert\Length(max=128)
     */
    private $remoteName;

    /**
     * This property is injected by Doctrine on Entity load.
     *
     * @var null|string
     * @Groups({"read"})
     */
    private $remoteUrl;

    /**
     * @var null|string
     * @ORM\Column(type="string", length=128)
     * @Assert\Length(max=128)
     * @Groups({"read", "write"})
     */
    private $name;

    /**
     * @var null|string
     * @ORM\Column(type="string", length=32)
     * @Assert\Choice(callback="getAllowedMimetypes", groups={"upload"}, strict=true)
     * @Assert\Length(max=32)
     */
    private $contentType;

    /**
     * @var null|string
     * @ORM\Column(type="string", length=16, nullable=false)
     * @Assert\Length(max=16)
     * @Groups({"read"})
     */
    private $filetype;

    /**
     * @var null|string
     * @ORM\Column(type="string", length=16)
     * @Assert\Length(max=16)
     * @Groups({"read"})
     */
    private $extension;

    /**
     * @ORM\Column(type="integer")
     *
     * @var null|int
     */
    private $size;

    /**
     * @var null|string
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(max=255)
     * @Groups({"read", "write"})
     */
    private $description;

    public function setId(string $id): void
    {
        $this->id = $id;
    }

    public function __toString(): string
    {
        return $this->getFullname();
    }

    public function setRemoteName(string $remoteName): void
    {
        $this->remoteName = $remoteName;
    }

    public function getRemoteName(): ?string
    {
        return $this->remoteName;
    }

    /**
     * @Groups({"read"})
     */
    public function getFullname(): string
    {
        return sprintf('%s.%s', $this->getName(), $this->getExtension());
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setContentType(string $contentType): void
    {
        $this->contentType = $contentType;
        $this->updateFiletype();
    }

    public function getContentType(): ?string
    {
        return $this->contentType;
    }

    public function setExtension(string $extension): void
    {
        $this->extension = $extension;
    }

    public function getExtension(): ?string
    {
        return $this->extension;
    }

    public function setSize(int $size): void
    {
        $this->size = $size;
    }

    public function getSize(): ?int
    {
        return $this->size;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function isImage(): bool
    {
        return false !== strpos($this->getContentType(), 'image/');
    }

    public function isAudio(): bool
    {
        return false !== strpos($this->getContentType(), 'audio/');
    }

    public function isVideo(): bool
    {
        return false !== strpos($this->getContentType(), 'video/');
    }

    public function isArchive(): bool
    {
        switch ($this->getContentType()) {
            case 'application/zip':
            case 'application/rar':
            case 'application/x-rar':
            case 'application/x-rar-compressed':
                return true;
            default:
                return false;
        }
    }

    public function isPDF(): bool
    {
        return false !== strpos($this->getContentType(), 'pdf');
    }

    private function updateFiletype(): void
    {
        if ($this->isImage()) {
            $this->filetype = self::FILETYPE_IMAGE;
        }

        if ($this->isAudio()) {
            $this->filetype = self::FILETYPE_AUDIO;
        }

        if ($this->isVideo()) {
            $this->filetype = self::FILETYPE_VIDEO;
        }

        if ($this->isArchive()) {
            $this->filetype = self::FILETYPE_ARCHIVE;
        }

        if ($this->isPDF()) {
            $this->filetype = self::FILETYPE_PDF;
        }
    }

    public function getFiletype(): ?string
    {
        return $this->filetype;
    }

    public function getAllowedMimetypes(): array
    {
        return static::$allowedMimeTypes;
    }

    /**
     * @return mixed
     */
    public function getRemoteUrl(): ?string
    {
        return $this->remoteUrl;
    }

    /**
     * @param mixed $remoteUrl
     */
    public function setRemoteUrl(string $remoteUrl)
    {
        $this->remoteUrl = $remoteUrl;
    }
}
