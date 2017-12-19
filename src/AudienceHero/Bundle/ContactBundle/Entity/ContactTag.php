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

namespace AudienceHero\Bundle\ContactBundle\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use AudienceHero\Bundle\CoreBundle\Behavior\Identifiable\IdentifiableEntity;
use AudienceHero\Bundle\CoreBundle\Behavior\Identifiable\IdentifiableInterface;
use AudienceHero\Bundle\CoreBundle\Behavior\Ownable\OwnableEntity;
use AudienceHero\Bundle\CoreBundle\Behavior\Ownable\OwnableInterface;
use AudienceHero\Bundle\CoreBundle\Entity\Tag;
use AudienceHero\Bundle\CoreBundle\Behavior\Timestampable\TimestampableEntity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\MaxDepth;

/**
 * ContactTag.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 * @ORM\Table(name="ah_contact_tag",
 *     uniqueConstraints={
 *         @ORM\UniqueConstraint(name="idx_unique_contact_tag", columns={"contact_id", "tag_id"})
 *     }
 * )
 * @ORM\Entity()
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
 *         "delete"={"method"="DELETE"},
 *     },
 * )
 */
class ContactTag implements OwnableInterface, IdentifiableInterface
{
    use \AudienceHero\Bundle\CoreBundle\Behavior\Identifiable\IdentifiableEntity;
    use \AudienceHero\Bundle\CoreBundle\Behavior\Ownable\OwnableEntity;
    use \AudienceHero\Bundle\CoreBundle\Behavior\Timestampable\TimestampableEntity;

    /**
     * @var null|Tag
     * @ORM\ManyToOne(targetEntity="AudienceHero\Bundle\CoreBundle\Entity\Tag")
     * @ORM\JoinColumn(name="tag_id", referencedColumnName="id", onDelete="CASCADE", nullable=false)
     * @Groups({"read", "write"})
     * @MaxDepth(1)
     */
    private $tag;

    /**
     * @var null|Contact
     * @ORM\ManyToOne(targetEntity="AudienceHero\Bundle\ContactBundle\Entity\Contact", inversedBy="tags")
     * @ORM\JoinColumn(name="contact_id", referencedColumnName="id", onDelete="CASCADE", nullable=false)
     */
    private $contact;

    /**
     * @return Tag|null
     */
    public function getTag(): ?Tag
    {
        return $this->tag;
    }

    /**
     * @param Tag|null $tag
     */
    public function setTag(?Tag $tag)
    {
        $this->tag = $tag;
        if (!$this->getOwner()) {
            $this->setOwner($tag->getOwner());
        }
    }

    /**
     * @return Contact|null
     */
    public function getContact(): ?Contact
    {
        return $this->contact;
    }

    /**
     * @param Contact|null $contact
     */
    public function setContact(?Contact $contact)
    {
        $this->contact = $contact;
    }
}
