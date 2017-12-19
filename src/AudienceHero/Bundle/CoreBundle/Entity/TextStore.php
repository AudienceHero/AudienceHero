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

namespace AudienceHero\Bundle\CoreBundle\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use AudienceHero\Bundle\CoreBundle\Behavior\HasSubjects\HasSubjectsInterface;
use AudienceHero\Bundle\CoreBundle\Behavior\HasSubjects\HasSubjectsEntity;
use AudienceHero\Bundle\CoreBundle\Behavior\Identifiable\IdentifiableEntity;
use AudienceHero\Bundle\CoreBundle\Behavior\Identifiable\IdentifiableInterface;
use AudienceHero\Bundle\CoreBundle\Behavior\Ownable\OwnableEntity;
use AudienceHero\Bundle\CoreBundle\Behavior\Ownable\OwnableInterface;
use AudienceHero\Bundle\CoreBundle\Behavior\Timestampable\TimestampableEntity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * TextStore.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 * @ORM\Entity()
 * @ORM\Table(name="ah_text_store")
 * @ApiResource(
 *     attributes={
 *         "normalization_context"={"groups"={"read"}, "enable_max_depth"=true},
 *         "denormalization_context"={"groups"={"write"}}
 *     },
 *     collectionOperations={
 *          "post"={"method"="POST"},
 *     },
 *     itemOperations={
 *         "get"={"method"="GET"},
 *         "put"={"method"="PUT"},
 *         "import"={"route_name"="api_text_stores_import"},
 *     },
 * )
 */
class TextStore implements OwnableInterface, IdentifiableInterface, HasSubjectsInterface
{
    use TimestampableEntity;
    use OwnableEntity;
    use IdentifiableEntity;
    use HasSubjectsEntity;

    const CONTENT_TYPE_CSV = 'text/csv';
    const CONTENT_TYPE_JSON = 'application/json';

    /**
     * @var null|string
     * @ORM\Column(type="string", length=127, nullable=false)
     * @Assert\Length(max=127)
     * @Assert\NotBlank()
     * @Groups({"read", "write"})
     */
    private $documentType;

    /**
     * @var null|string
     * @ORM\Column(type="string", length=127, nullable=false)
     * @Assert\Length(max=127)
     * @Groups({"private_read", "write"})
     */
    private $contentType;

    /**
     * @var null|string
     * @ORM\Column(type="text", nullable=false)
     * @Groups({"write"})
     */
    private $text;

    /**
     * @var array
     * @ORM\Column(type="json_array", nullable=false)
     * @Groups({"private_read", "write"})
     */
    private $metadata = [];

    public function setContentType(string $contentType): void
    {
        $this->contentType = $contentType;
    }

    public function getContentType(): ?string
    {
        return $this->contentType;
    }

    /**
     * setText sets the text of the TextStore.
     *
     * It converts the text encoding to UTF-8, otherwise the database might not know how
     * to handle the input.
     */
    public function setText(string $text): void
    {
        $encoding = mb_detect_encoding($text, mb_detect_order(), true);
        if ('UTF-8' === $encoding) {
            $this->text = $text;

            return;
        }

        if (false === $encoding) {
            $result = @iconv('ISO-8859-1', 'UTF-8', $text);
            if (false !== $result) {
                $this->text = $result;

                return;
            }
        } else {
            $result = @iconv($encoding, 'UTF-8', $text);
            if (false !== $result) {
                $this->text = $result;

                return;
            }
        }

        $this->text = $text;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    /**
     * @return array
     */
    public function getMetadata(): array
    {
        return $this->metadata;
    }

    /**
     * @param array $metadata
     */
    public function setMetadata(array $metadata): void
    {
        $this->metadata = $metadata;
    }

    public function addMetadata(string $key, $value): void
    {
        $this->metadata[$key] = $value;
    }

    /**
     * @return null|string
     */
    public function getDocumentType(): ?string
    {
        return $this->documentType;
    }

    /**
     * @param null|string $documentType
     */
    public function setDocumentType(?string $documentType): void
    {
        $this->documentType = $documentType;
    }
}
