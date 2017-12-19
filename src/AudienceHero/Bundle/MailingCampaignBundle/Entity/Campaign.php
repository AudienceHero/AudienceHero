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

namespace AudienceHero\Bundle\MailingCampaignBundle\Entity;

use AppBundle\Domain\CampaignRecipientsFilters;
use AudienceHero\Bundle\ContactBundle\Entity\ContactsGroup;
use AudienceHero\Bundle\CoreBundle\Behavior\Identifiable\IdentifiableEntity;
use AudienceHero\Bundle\CoreBundle\Behavior\Identifiable\IdentifiableInterface;
use AudienceHero\Bundle\CoreBundle\Behavior\Ownable\OwnableEntity;
use AudienceHero\Bundle\CoreBundle\Behavior\Ownable\OwnableInterface;
use AudienceHero\Bundle\CoreBundle\Behavior\Searchable\SearchableEntity;
use AudienceHero\Bundle\CoreBundle\Behavior\Timestampable\TimestampableEntity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\MaxDepth;
use Symfony\Component\Validator\Constraints as Assert;

abstract class Campaign implements \AudienceHero\Bundle\CoreBundle\Behavior\Ownable\OwnableInterface, IdentifiableInterface
{
    const STATUS_DRAFT = 'draft';
    const STATUS_PENDING = 'pending';
    const STATUS_DELIVERING = 'delivering';
    const STATUS_DELIVERED = 'delivered';

    use \AudienceHero\Bundle\CoreBundle\Behavior\Ownable\OwnableEntity;
    use \AudienceHero\Bundle\CoreBundle\Behavior\Timestampable\TimestampableEntity;
    use SearchableEntity;
    use \AudienceHero\Bundle\CoreBundle\Behavior\Identifiable\IdentifiableEntity;

    /**
     * @var string
     * @ORM\Column(type="string", length=15, nullable=false)
     * @Assert\Length(max=15)
     * @Groups({"read"})
     */
    protected $status = self::STATUS_DRAFT;

    /**
     * @var array|CampaignRecipientsFilters
     * @ORM\Column(type="json_array", nullable=true)
     */
    protected $recipientsFilters = [];

    /**
     * @var null|ContactsGroup
     * @ORM\ManyToOne(targetEntity="AudienceHero\Bundle\ContactBundle\Entity\ContactsGroup")
     * @ORM\JoinColumn(name="contacts_group_id", referencedColumnName="id", onDelete="SET NULL", nullable=true)
     * @Assert\NotNull()
     * @MaxDepth(1)
     * @Groups({"read", "write"})
     */
    protected $contactsGroup;

    /**
     * @param mixed $contactsGroup
     */
    public function setContactsGroup(?ContactsGroup $contactsGroup): void
    {
        $this->contactsGroup = $contactsGroup;
    }

    /**
     * @return mixed
     */
    public function getContactsGroup(): ?ContactsGroup
    {
        return $this->contactsGroup;
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function isStatusDraft(): bool
    {
        return self::STATUS_DRAFT === $this->getStatus();
    }

    public function isStatusDelivered(): bool
    {
        return self::STATUS_DELIVERED === $this->getStatus();
    }

    public function isStatusDelivering(): bool
    {
        return self::STATUS_DELIVERING === $this->getStatus();
    }

    public function isStatusPending(): bool
    {
        return self::STATUS_PENDING === $this->getStatus();
    }

    /**
     * @param array|CampaignRecipientsFilters $recipientsFilters
     */
    public function setRecipientsFilters($recipientsFilters): void
    {
        $this->recipientsFilters = $recipientsFilters;
    }

    /**
     * @return CampaignRecipientsFilters|array
     */
    public function getRecipientsFilters()
    {
        return $this->recipientsFilters;
    }
}
