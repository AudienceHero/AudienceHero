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
use AudienceHero\Bundle\ContactBundle\Util\SocialHandleCleaner;
use AudienceHero\Bundle\CoreBundle\Bridge\ApiPlatform\Filter\SearchFilter;
use AudienceHero\Bundle\CoreBundle\Behavior\Identifiable\IdentifiableEntity;
use AudienceHero\Bundle\CoreBundle\Behavior\Identifiable\IdentifiableInterface;
use AudienceHero\Bundle\CoreBundle\Behavior\Ownable\OwnableEntity;
use AudienceHero\Bundle\CoreBundle\Behavior\Ownable\OwnableInterface;
use AudienceHero\Bundle\CoreBundle\Behavior\Searchable\SearchableEntity;
use AudienceHero\Bundle\CoreBundle\Behavior\Timestampable\TimestampableEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Util\Inflector;
use Doctrine\ORM\Mapping as ORM;
use MarcW\CCCN\CCCN;
use Misd\PhoneNumberBundle\Validator\Constraints\PhoneNumber;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity as AssertUniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\MaxDepth;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * @AssertUniqueEntity(fields={"email", "owner"}, groups={"Default"})
 * @ORM\Table(name="ah_contact")
 * @ORM\Entity(repositoryClass="AudienceHero\Bundle\ContactBundle\Repository\ContactRepository")
 * @ApiResource(
 *     attributes={
 *         "filters"={SearchFilter::class, "audience_hero.api.order.timestampable", "audiencehero_contact.filter.order", "audiencehero_contact.filter.search", "audiencehero_contact.filter.date"},
 *         "normalization_context"={"groups"={"read", "contact.read"}},
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
class Contact implements OwnableInterface, \AudienceHero\Bundle\CoreBundle\Behavior\Identifiable\IdentifiableInterface
{
    use \AudienceHero\Bundle\CoreBundle\Behavior\Ownable\OwnableEntity;
    use \AudienceHero\Bundle\CoreBundle\Behavior\Searchable\SearchableEntity;
    use \AudienceHero\Bundle\CoreBundle\Behavior\Timestampable\TimestampableEntity;
    use IdentifiableEntity;

    /** @var array */
    public static $csvHeaders = [
        'name',
        'email',
        'homepage',
        'country',
        'postal_code',
        'phone',
        'city',
        'address',
        'salutation_name',
        'company_name',
        'notes',
        'sn_twitter',
        'sn_facebook',
        'sn_instagram',
        'sn_soundcloud',
    ];

    /**
     * @var null|string
     * @ORM\Column(type="string", length=64, nullable=true)
     * @Assert\Length(max=64, groups={"Default", "import", "optin"})
     * @Groups({"read", "write", "contact.read"})
     */
    private $name;

    /**
     * @var null|string
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"read", "write", "contact.read"})
     * @Assert\Email(groups={"Default", "import", "optin"})
     * @Assert\Length(max=255, groups={"Default", "import", "optin"})
     */
    private $email;

    /**
     * @var null|string
     * @Groups({"read", "write", "contact.read"})
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Url(groups={"Default", "AcquisitionFreeDownload", "import"})
     * @Assert\Length(max=255, groups={"Default", "AcquisitionFreeDownload", "import"})
     */
    private $homepage;

    /**
     * @var null|string
     * @Groups({"read", "write", "contact.read"})
     * @ORM\Column(type="string", length=64, nullable=true)
     * @Assert\Country(groups={"Default", "AcquisitionFreeDownload", "import", "optin"});
     * @Assert\Length(max=64, groups={"Default", "AcquisitionFreeDownload", "import", "optin"})
     */
    private $country;

    /**
     * @var null|string
     * @Groups({"read", "write", "contact.read"})
     * @ORM\Column(type="string", length=32, nullable=true)
     * @Assert\Length(max=32, groups={"Default", "AcquisitionFreeDownload", "import"})
     */
    private $postalCode;

    /**
     * @var null|string
     * @Groups({"read", "write", "contact.read"})
     * @ORM\Column(type="phone_number", nullable=true)
     * @PhoneNumber(groups={"Default", "AcquisitionFreeDownload", "import", "optin"})
     */
    private $phone;

    /**
     * @var null|string
     * @Groups({"read", "write", "contact.read"})
     * @ORM\Column(type="string", length=64, nullable=true)
     * @Assert\Length(max=64, groups={"Default", "AcquisitionFreeDownload", "import", "optin"})
     */
    private $city;

    /**
     * @var null|string
     * @Groups({"read", "write", "contact.read"})
     * @ORM\Column(type="string", length=64, nullable=true)
     * @Assert\Length(max=64, groups={"Default", "AcquisitionFreeDownload", "import"})
     */
    private $address;

    /**
     * @var null|string
     * @Groups({"contact.read"})
     * @ORM\Column(type="string", length=64, nullable=true)
     * @Assert\Length(max=64, groups={"Default", "AcquisitionFreeDownload", "import"})
     */
    private $salutationName;

    /**
     * @var null|string
     * @Groups({"contact.read"})
     * @ORM\Column(type="string", length=64, nullable=true)
     * @Assert\Length(max=64, groups={"Default", "AcquisitionFreeDownload", "import"})
     */
    private $companyName;

    /**
     * @ORM\Column(type="boolean", nullable=false)
     * @Groups({"contact.read"})
     *
     * @var bool
     */
    private $isCompany = false;

    /**
     * @var null|string
     * @Groups({"contact.read"})
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(max=255, groups={"Default", "AcquisitionFreeDownload", "import"})
     */
    private $notes;

    /**
     * @ORM\OneToMany(targetEntity="ContactsGroupContact", mappedBy="contact", orphanRemoval=true, cascade={"all"})
     * @Groups({"read", "write", "contact.read"})
     * @MaxDepth(1)
     *
     * @var Collection
     */
    private $groups;

    /**
     * @var null|string
     * @Groups({"contact.read"})
     * @ORM\Column(type="string", length=64, nullable=true)
     * @Assert\Length(max=64, groups={"Default", "AcquisitionFreeDownload", "import"})
     */
    private $snTwitter;

    /**
     * @var null|string
     * @Groups({"contact.read"})
     * @ORM\Column(type="string", length=64, nullable=true)
     * @Assert\Length(max=64, groups={"Default", "AcquisitionFreeDownload", "import"})
     */
    private $snFacebook;

    /**
     * @var null|string
     * @Groups({"contact.read"})
     * @ORM\Column(type="string", length=64, nullable=true)
     * @Assert\Length(max=64, groups={"Default", "AcquisitionFreeDownload", "import"})
     */
    private $snInstagram;

    /**
     * @var null|string
     * @Groups({"contact.read"})
     * @ORM\Column(type="string", length=64, nullable=true)
     * @Assert\Length(max=64, groups={"Default", "AcquisitionFreeDownload", "import"})
     */
    private $snSoundcloud;

    /**
     * @var null|string
     * @Groups({"contact.read"})
     * @ORM\Column(type="string", length=64, nullable=true)
     * @Assert\Length(max=64, groups={"Default", "AcquisitionFreeDownload", "import"})
     */
    private $snMixcloud;

    /**
     * @var null|string
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(max=255, groups={"Default", "AcquisitionFreeDownload", "import"})
     */
    private $importGoogleId;

    /**
     * @var null|string
     * @ORM\Column(type="decimal", precision=10, scale=7, nullable=true)
     */
    private $latitude;

    /**
     * @var null|string
     * @ORM\Column(type="decimal", precision=10, scale=7, nullable=true)
     */
    private $longitude;

    /**
     * @ORM\OneToMany(targetEntity="AudienceHero\Bundle\ContactBundle\Entity\ContactTag", orphanRemoval=true, mappedBy="contact", cascade={"all"});
     * @Groups({"read", "write"})
     * @MaxDepth(1)
     */
    private $tags;

    public function __construct()
    {
        $this->groups = new ArrayCollection();
        $this->tags = new ArrayCollection();
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getSalutationName(): ?string
    {
        return $this->salutationName ?: $this->getName();
    }

    public function setEmail(?string $email): void
    {
        $this->email = filter_var(strtolower(trim($email)), FILTER_SANITIZE_EMAIL);
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setNotes(?string $notes): void
    {
        $this->notes = $notes;
    }

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function setCountry(?string $country): void
    {
        if (!$country) {
            $this->country = null;

            return;
        }

        $this->country = CCCN::getCountryCode($country);
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCity(?string $city): void
    {
        $this->city = $city;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setAddress(?string $address): void
    {
        $this->address = $address;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setPhone(?string $phone): void
    {
        $this->phone = $phone;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function getGroups(): iterable
    {
        return $this->groups;
    }

    public function setSnTwitter(?string $snTwitter): void
    {
        $this->snTwitter = SocialHandleCleaner::twitter($snTwitter);
    }

    public function getSnTwitter(): ?string
    {
        return $this->snTwitter;
    }

    public function setSnFacebook(?string $snFacebook): void
    {
        $this->snFacebook = SocialHandleCleaner::facebook($snFacebook);
    }

    public function getSnFacebook(): ?string
    {
        return $this->snFacebook;
    }

    public function setSnInstagram(?string $snInstagram): void
    {
        $this->snInstagram = SocialHandleCleaner::instagram($snInstagram);
    }

    public function getSnInstagram(): ?string
    {
        return $this->snInstagram;
    }

    public function setSnSoundcloud(?string $snSoundcloud): void
    {
        $this->snSoundcloud = SocialHandleCleaner::soundcloud($snSoundcloud);
    }

    public function getSnSoundcloud(): ?string
    {
        return $this->snSoundcloud;
    }

    public function setSnMixcloud(?string $snMixcloud): void
    {
        $this->snMixcloud = SocialHandleCleaner::mixcloud($snMixcloud);
    }

    public function getSnMixcloud(): ?string
    {
        return $this->snMixcloud;
    }

    public function setImportGoogleId(?string $importGoogleId): void
    {
        $this->importGoogleId = $importGoogleId;
    }

    public function getImportGoogleId(): ?string
    {
        return $this->importGoogleId;
    }

    public function merge(Contact $contact): void
    {
        if ($contact->getName()) {
            $this->setName($contact->getName());
        }
        if ($contact->getEmail()) {
            $this->setEmail($contact->getEmail());
        }
        if ($contact->getPhone()) {
            $this->setPhone($contact->getPhone());
        }
        if ($contact->getHomepage()) {
            $this->setHomepage($contact->getHomepage());
        }
        if ($contact->getOwner()) {
            $this->setOwner($contact->getOwner());
        }
        if ($contact->getCity()) {
            $this->setCity($contact->getCity());
        }
        if ($contact->getCountry()) {
            $this->setCountry($contact->getCountry());
        }
        if ($contact->getPostalCode()) {
            $this->setPostalCode($contact->getPostalCode());
        }
        if ($contact->getAddress()) {
            $this->setAddress($contact->getAddress());
        }
        if ($contact->getSalutationName()) {
            $this->setSalutationName($contact->getSalutationName());
        }
        if ($contact->getImportGoogleId()) {
            $this->setImportGoogleId($contact->getImportGoogleId());
        }
        if ($contact->getSnTwitter()) {
            $this->setSnTwitter($contact->getSnTwitter());
        }
        if ($contact->getSnFacebook()) {
            $this->setSnFacebook($contact->getSnFacebook());
        }
        if ($contact->getSnInstagram()) {
            $this->setSnInstagram($contact->getSnInstagram());
        }
        if ($contact->getSnMixcloud()) {
            $this->setSnMixcloud($contact->getSnMixcloud());
        }
        if ($contact->getSnSoundcloud()) {
            $this->setSnSoundcloud($contact->getSnSoundcloud());
        }
        if ($contact->getNotes()) {
            $this->setNotes($contact->getNotes());
        }
        if ($contact->getCompanyName()) {
            $this->setCompanyName($contact->getCompanyName());
        }
    }

    public function displayName(): ?string
    {
        return $this->getName() ?: $this->getEmail();
    }

    public function setIsCompany(?string $isCompany): void
    {
        $this->isCompany = $isCompany;
    }

    public function getIsCompany(): bool
    {
        return $this->isCompany;
    }

    public function setCompanyName(?string $companyName): void
    {
        $this->companyName = $companyName;
    }

    public function getCompanyName(): ?string
    {
        return $this->companyName;
    }

    public function setHomepage(?string $homepage): void
    {
        $this->homepage = $homepage;
    }

    public function getHomepage(): ?string
    {
        return $this->homepage;
    }

    public function setSalutationName(?string $salutationName): void
    {
        $this->salutationName = $salutationName;
    }

    public function setPostalCode(?string $postalCode): void
    {
        $this->postalCode = $postalCode;
    }

    public function getPostalCode(): ?string
    {
        return $this->postalCode;
    }

    public function toCsvArray(): array
    {
        $d = [];
        foreach (self::$csvHeaders as $key) {
            $method = sprintf('get%s', Inflector::camelize($key));
            $d[$key] = $this->$method();
        }

        return $d;
    }

    public function setLatitude(?string $latitude): void
    {
        $this->latitude = $latitude;
    }

    public function getLatitude(): ?string
    {
        return $this->latitude;
    }

    public function setLongitude(?string $longitude): void
    {
        $this->longitude = $longitude;
    }

    public function getLongitude(): ?string
    {
        return $this->longitude;
    }

    /**
     * @Assert\Callback(groups={"Default", "optin"})
     */
    public function validateOptin(ExecutionContextInterface $context, $payload)
    {
        $phone = $this->getPhone();
        $email = $this->getEmail();

        if (!$phone && !$email) {
            $notBlank = new Assert\NotBlank();

            $context->buildViolation($notBlank->message)->atPath('phone')->addViolation();
            $context->buildViolation($notBlank->message)->atPath('email')->addViolation();
        }
    }

    /**
     * @return mixed
     */
    public function getTags(): iterable
    {
        return $this->tags;
    }

    /**
     * @param mixed $tags
     */
    public function setTags(iterable $tags): void
    {
        /** @var ContactTag $tag */
        foreach ($this->tags as $tag) {
            // Remove element from collection and let the orphanRemoval works its magic.
            if (is_array($tags)) {
                if (!in_array($tag, $tags, true)) {
                    $this->removeTag($tag);
                }
            } elseif ($tags instanceof Collection) {
                if (!$tags->contains($tag)) {
                    $this->removeTag($tag);
                }
            }
        }

        foreach ($tags as $tag) {
            if (!$this->tags->contains($tag)) {
                $this->addTag($tag);
            }
        }
    }

    public function addTag(ContactTag $contactTag)
    {
        $this->tags->add($contactTag);
        $contactTag->setContact($this);
    }

    public function removeTag(ContactTag $contactTag)
    {
        $this->tags->removeElement($contactTag);
        $contactTag->setContact(null);
        $contactTag->setTag(null);
    }

    public function setGroups(iterable $groups)
    {
        /** @var ContactsGroupContact $group */
        foreach ($this->groups as $group) {
            // Remove element from collection and let the orphanRemoval works its magic.
            if (is_array($groups)) {
                if (!in_array($group, $groups, true)) {
                    $this->removeGroup($group);
                }
            } elseif ($groups instanceof Collection) {
                if (!$groups->contains($group)) {
                    $this->removeGroup($group);
                }
            }
        }

        foreach ($groups as $group) {
            if (!$this->groups->contains($group)) {
                $this->addGroup($group);
            }
        }
    }

    public function addGroup(ContactsGroupContact $contactsGroup)
    {
        $this->groups->add($contactsGroup);
        $contactsGroup->setContact($this);
    }

    public function removeGroup(ContactsGroupContact $contactsGroup)
    {
        $this->groups->removeElement($contactsGroup);
        $contactsGroup->setContact(null);
    }
}
