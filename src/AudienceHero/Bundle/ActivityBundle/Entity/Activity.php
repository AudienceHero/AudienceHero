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
use AudienceHero\Bundle\CoreBundle\Behavior\Metadata\HasPrivateMetadataInterface;
use AudienceHero\Bundle\CoreBundle\Behavior\Metadata\HasPrivateMetadataTrait;
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
 * Activity.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 *
 * @ORM\Table(name="ah_activity")
 * @ORM\Entity(repositoryClass="AudienceHero\Bundle\ActivityBundle\Repository\ActivityRepository")
 * @ApiResource(
 *     attributes={
 *         "order"={"createdAt": "DESC"},
 *         "filters"={
 *             "audiencehero_activity.json_filter",
 *             "audience_hero.api.order.timestampable",
 *             "audiencehero_activity.order_filter",
 *             "audiencehero_activity.type_search_filter",
 *             "audiencehero_activity.date_filter",
 *         },
 *         "normalization_context"={"groups"={"read"}, "enable_max_depth"=true},
 *         "denormalization_context"={"groups"={"write"}, "enable_max_depth"=true}
 *     },
 *     collectionOperations={
 *         "get"={"method"="GET"},
 *         "post"={"method"="POST"},
 *     },
 *     itemOperations={
 *         "get"={"method"="get"}
 *     },
 * )
 */
class Activity implements \AudienceHero\Bundle\CoreBundle\Behavior\Ownable\OwnableInterface, IdentifiableInterface, HasSubjectsInterface, HasPrivateMetadataInterface
{
    use IdentifiableEntity;
    use OwnableEntity;
    use \AudienceHero\Bundle\CoreBundle\Behavior\Timestampable\TimestampableEntity;
    use HasSubjectsEntity;
    use DeviceTrait;
    use LocationTrait;
    use RefererTrait;
    use \AudienceHero\Bundle\CoreBundle\Behavior\Metadata\HasPrivateMetadataTrait;

    /**
     * @var null|string
     * @Assert\Length(max=32)
     * @ORM\Column(name="type", type="string", length=128, nullable=false)
     * @Groups({"read", "write"})
     */
    private $type;

    /**
     * @var null|string
     * @Assert\Length(max=45)
     * @ORM\Column(name="ip", type="string", length=45, nullable=true)
     * @Groups({"read"})
     */
    private $ip;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_spam", type="boolean", nullable=false)
     * @Groups({"read"})
     */
    private $isSpam = false;

    /**
     * @var array
     *
     * @ORM\Column(name="request", type="json_array", nullable=true)
     * @Groups({"read"})
     */
    private $request = [];

    public function setIp(string $ip): void
    {
        $this->ip = $ip;
    }

    public function getIp(): ?string
    {
        return $this->ip;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setRequest(array $request)
    {
        $this->request = $request;
    }

    public function getRequest(): array
    {
        return $this->request;
    }

    public function setIsSpam(bool $isSpam): void
    {
        $this->isSpam = $isSpam;
    }

    public function getIsSpam(): bool
    {
        return $this->isSpam;
    }
}
