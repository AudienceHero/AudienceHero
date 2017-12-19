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
use AudienceHero\Bundle\CoreBundle\Behavior\Referenceable\ReferenceableEntity;
use AudienceHero\Bundle\CoreBundle\Behavior\Referenceable\ReferenceableInterface;
use AudienceHero\Bundle\CoreBundle\Behavior\Timestampable\TimestampableEntity;
use AudienceHero\Bundle\FileBundle\Entity\File;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * ContactsGroup.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 * @ORM\Table(name="ah_contacts_group")
 * @ORM\Entity(repositoryClass="AudienceHero\Bundle\ContactBundle\Repository\ContactsGroupRepository")
 * @ApiResource(
 *     attributes={
 *         "normalization_context"={"groups"={"read"}},
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
 *     },
 * )
 */
class ContactsGroup implements OwnableInterface, \AudienceHero\Bundle\CoreBundle\Behavior\Referenceable\ReferenceableInterface, IdentifiableInterface
{
    use TimestampableEntity;
    use \AudienceHero\Bundle\CoreBundle\Behavior\Ownable\OwnableEntity;
    use \AudienceHero\Bundle\CoreBundle\Behavior\Referenceable\ReferenceableEntity;
    use \AudienceHero\Bundle\CoreBundle\Behavior\Identifiable\IdentifiableEntity;

    /**
     * @var null|string
     * @ORM\Column(type="string", length=64, nullable=false)
     * @Assert\Length(max=64)
     * @Groups({"contact.read", "read", "write"})
     */
    private $name;

    /**
     * @var null|string
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(max=255)
     * @Groups({"contact.read", "read", "write"})
     */
    private $description;

    /**
     * @ORM\OneToMany(targetEntity="ContactsGroupContact", mappedBy="group", cascade={"persist"})
     *
     * @var Collection
     */
    private $contacts;

    public function __construct()
    {
        $this->contacts = new ArrayCollection();
    }

    public function countOptins(): int
    {
        return $this->contacts->filter(function (ContactsGroupContact $cgc) {
            return $cgc->isOptin();
        })->count();
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getContacts(): iterable
    {
        return $this->contacts;
    }
}
