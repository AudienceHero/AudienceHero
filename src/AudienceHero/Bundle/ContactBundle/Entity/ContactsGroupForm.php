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
use AudienceHero\Bundle\CoreBundle\Behavior\Linkable\LinkableEntity;
use AudienceHero\Bundle\CoreBundle\Behavior\Linkable\LinkableInterface;
use AudienceHero\Bundle\CoreBundle\Behavior\Ownable\OwnableEntity;
use AudienceHero\Bundle\CoreBundle\Behavior\Ownable\OwnableInterface;
use AudienceHero\Bundle\CoreBundle\Behavior\Publishable\PublishableEntity;
use AudienceHero\Bundle\CoreBundle\Behavior\Publishable\PublishableInterface;
use AudienceHero\Bundle\CoreBundle\Behavior\Referenceable\ReferenceableEntity;
use AudienceHero\Bundle\CoreBundle\Behavior\Referenceable\ReferenceableInterface;
use AudienceHero\Bundle\CoreBundle\Behavior\Timestampable\TimestampableEntity;
use AudienceHero\Bundle\FileBundle\Entity\File;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\MaxDepth;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * ContactsGroupForm.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 * @ORM\Table(name="ah_contacts_group_form")
 * @ORM\Entity()
 * @ApiResource(
 *     attributes={
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
 *         "optin"={"route_name"="api_contacts_group_forms_optin"},
 *     }
 * )
 */
class ContactsGroupForm implements OwnableInterface, LinkableInterface, ReferenceableInterface, PublishableInterface, IdentifiableInterface
{
    use IdentifiableEntity;
    use LinkableEntity;
    use OwnableEntity;
    use PublishableEntity;
    use ReferenceableEntity;
    use TimestampableEntity;

    /**
     * @var null|string
     * @ORM\Column(type="string", length=64, nullable=false)
     * @Assert\NotBlank()
     * @Assert\Length(max=64)
     * @Groups({"read", "write"})
     */
    private $name;

    /**
     * @var null|string
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(max=255)
     * @Groups({"read", "write"})
     */
    private $description;

    /**
     * @var null|ContactsGroup
     * @ORM\ManyToOne(targetEntity="AudienceHero\Bundle\ContactBundle\Entity\ContactsGroup")
     * @ORM\JoinColumn(name="contacts_group_id", referencedColumnName="id", onDelete="CASCADE", nullable=false)
     * @Assert\NotNull()
     * @Groups({"read", "write"})
     * @MaxDepth(1)
     */
    private $contactsGroup;

    /**
     * @var null|File
     * @ORM\ManyToOne(targetEntity="AudienceHero\Bundle\FileBundle\Entity\File")
     * @ORM\JoinColumn(name="image_id", referencedColumnName="id", onDelete="SET NULL", nullable=true)
     * @MaxDepth(1)
     * @Groups({"read"})
     */
    private $image;

    /**
     * @var bool
     * @ORM\Column(type="boolean", nullable=false)
     * @Groups({"read", "write"})
     * @Assert\Type("bool")
     */
    private $askEmail = true;

    /**
     * @var bool
     * @ORM\Column(type="boolean", nullable=false)
     * @Groups({"read", "write"})
     * @Assert\Type("bool")
     */
    private $askName = true;

    /**
     * @var bool
     * @ORM\Column(type="boolean", nullable=false)
     * @Groups({"read", "write"})
     * @Assert\Type("bool")
     */
    private $askCity = true;

    /**
     * @var bool
     * @ORM\Column(type="boolean", nullable=false)
     * @Groups({"read", "write"})
     * @Assert\Type("bool")
     */
    private $askCountry = true;

    /**
     * @var bool
     * @ORM\Column(type="boolean", nullable=false)
     * @Groups({"read", "write"})
     * @Assert\Type("bool")
     */
    private $askPhone = true;

    /**
     * @var bool
     * @ORM\Column(type="boolean", nullable=false)
     * @Groups({"read", "write"})
     * @Assert\Type("bool")
     */
    private $displayQRCode = true;

    /**
     * @var null|string
     * @Groups({"read"})
     */
    private $guessedCountry;

    /**
     * @var null|string
     * @Groups({"read"})
     */
    private $guessedCity;

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
    public function setName(string $name)
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
    public function setDescription(?string $description)
    {
        $this->description = $description;
    }

    /**
     * @return File|null
     */
    public function getImage(): ?File
    {
        return $this->image;
    }

    /**
     * @param File|null $image
     */
    public function setImage(?File $image)
    {
        $this->image = $image;
    }

    /**
     * @return ContactsGroup|null
     */
    public function getContactsGroup(): ?ContactsGroup
    {
        return $this->contactsGroup;
    }

    /**
     * @param ContactsGroup|null $contactsGroup
     */
    public function setContactsGroup(?ContactsGroup $contactsGroup)
    {
        $this->contactsGroup = $contactsGroup;
    }

    /**
     * @return bool
     */
    public function getAskEmail(): bool
    {
        return $this->askEmail;
    }

    /**
     * @param bool $askEmail
     */
    public function setAskEmail(bool $askEmail)
    {
        $this->askEmail = $askEmail;
    }

    /**
     * @param bool $askName
     */
    public function setAskName(bool $askName)
    {
        $this->askName = $askName;
    }

    /**
     * @return bool
     */
    public function getAskName(): bool
    {
        return $this->askName;
    }

    /**
     * @return bool
     */
    public function getAskCity(): bool
    {
        return $this->askCity;
    }

    /**
     * @param bool $askCity
     */
    public function setAskCity(bool $askCity)
    {
        $this->askCity = $askCity;
    }

    /**
     * @return bool
     */
    public function getAskCountry(): bool
    {
        return $this->askCountry;
    }

    /**
     * @param bool $askCountry
     */
    public function setAskCountry(bool $askCountry)
    {
        $this->askCountry = $askCountry;
    }

    /**
     * @return bool
     */
    public function getAskPhone(): bool
    {
        return $this->askPhone;
    }

    /**
     * @param bool $askPhone
     */
    public function setAskPhone(bool $askPhone)
    {
        $this->askPhone = $askPhone;
    }

    /**
     * @return bool
     */
    public function getDisplayQRCode(): bool
    {
        return $this->displayQRCode;
    }

    /**
     * @param bool $displayQRCode
     */
    public function setDisplayQRCode(bool $displayQRCode)
    {
        $this->displayQRCode = $displayQRCode;
    }

    /**
     * @return null|string
     */
    public function getGuessedCountry(): ?string
    {
        return $this->guessedCountry;
    }

    /**
     * @param null|string $guessedCountry
     */
    public function setGuessedCountry(?string $guessedCountry): void
    {
        $this->guessedCountry = $guessedCountry;
    }

    /**
     * @return null|string
     */
    public function getGuessedCity(): ?string
    {
        return $this->guessedCity;
    }

    /**
     * @param null|string $guessedCity
     */
    public function setGuessedCity(?string $guessedCity): void
    {
        $this->guessedCity = $guessedCity;
    }
}
