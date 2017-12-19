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
use AudienceHero\Bundle\CoreBundle\Behavior\Identifiable\IdentifiableEntity;
use AudienceHero\Bundle\CoreBundle\Behavior\Identifiable\IdentifiableInterface;
use AudienceHero\Bundle\CoreBundle\Behavior\Ownable\OwnableEntity;
use AudienceHero\Bundle\CoreBundle\Behavior\Ownable\OwnableInterface;
use AudienceHero\Bundle\CoreBundle\Behavior\Timestampable\TimestampableEntity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Tag.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 * @ORM\Entity()
 * @ApiResource(
 *     attributes={
 *         "filters"={"audience_hero.api.order.timestampable"},
 *         "normalization_context"={"groups"={"read"}, "enable_max_depth"=true},
 *         "denormalization_context"={"groups"={"write"}}
 *     },
 *     collectionOperations={
 *         "get"={"method"="GET"},
 *         "post"={"method"="POST"},
 *     },
 *     itemOperations={
 *         "get"={"method"="GET"},
 *         "put"={"method"="PUT"},
 *         "delete"={"method"="DELETE"},
 *     }
 * )
 * @ORM\Table(name="ah_tag")
 */
class Tag implements OwnableInterface, IdentifiableInterface
{
    use TimestampableEntity;
    use OwnableEntity;
    use IdentifiableEntity;

    /**
     * @var null|string
     * @ORM\Column(type="string", length=32, nullable=false)
     * @Groups({"read", "write"})
     * @Assert\NotBlank()
     * @Assert\Length(max=32)
     */
    private $name;

    /**
     * @var null|string
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"read", "write"})
     * @Assert\Length(max=255)
     */
    private $description;

    /**
     * @return null|string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param null|string $name
     */
    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return null|string
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param null|string $description
     */
    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }
}
