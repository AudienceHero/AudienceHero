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

namespace AudienceHero\Bundle\ActivityBundle\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use AudienceHero\Bundle\CoreBundle\Behavior\Identifiable\IdentifiableEntity;
use AudienceHero\Bundle\CoreBundle\Behavior\Identifiable\IdentifiableInterface;
use AudienceHero\Bundle\CoreBundle\Behavior\Ownable\OwnableEntity;
use AudienceHero\Bundle\CoreBundle\Behavior\Ownable\OwnableInterface;
use AudienceHero\Bundle\CoreBundle\Behavior\Timestampable\TimestampableEntity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Aggregate.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 * @ORM\Table(name="ah_aggregate")
 * @ORM\Entity(repositoryClass="AudienceHero\Bundle\ActivityBundle\Repository\AggregateRepository")
 * @ApiResource(
 *     attributes={
 *         "order"={"createdAt": "DESC"},
 *         "filters"={
 *              "audiencehero_activity.date_filter",
 *              "audiencehero_activity.type_search_filter",
 *              "audiencehero_activity.subject_search_filter",
 *          },
 *         "normalization_context"={"groups"={"read"}, "enable_max_depth"=true}
 *     },
 *     collectionOperations={
 *         "get"={"method"="GET"},
 *     },
 *     itemOperations={
 *         "get"={"method"="GET"},
 *     },
 * )
 */
class Aggregate implements \AudienceHero\Bundle\CoreBundle\Behavior\Ownable\OwnableInterface, IdentifiableInterface
{
    use \AudienceHero\Bundle\CoreBundle\Behavior\Ownable\OwnableEntity;
    use TimestampableEntity;
    use IdentifiableEntity;

    /**
     * @ORM\Column(type="guid", nullable=false)
     * @Groups({"read"})
     */
    private $subjectId;

    /**
     * @var null|string
     * @ORM\Column(type="string", length=255, nullable=false)
     * @Groups({"read"})
     */
    private $type;

    /**
     * @ORM\Column(type="json_document", options={"jsonb": true}, length=255, nullable=false)
     * @Groups({"read"})
     */
    private $data = [];

    /**
     * @return mixed
     */
    public function getId(): ?string
    {
        return $this->id;
    }

    /**
     * @return null|string
     */
    public function getSoftReferenceKey(): ?string
    {
        return 'aggregates';
    }

    /**
     * @return mixed
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @param mixed $data
     */
    public function setData(array $data): void
    {
        $this->data = $data;
    }

    public function addData(string $key, $data): void
    {
        $this->data[$key] = $data;
    }

    /**
     * @return null|string
     */
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * @param null|string $type
     */
    public function setType(?string $type): void
    {
        $this->type = $type;
    }

    /**
     * @return mixed
     */
    public function getSubjectId(): ?string
    {
        return $this->subjectId;
    }

    /**
     * @param mixed $subjectId
     */
    public function setSubjectId(string $subjectId): void
    {
        $this->subjectId = $subjectId;
    }
}
