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

use AudienceHero\Bundle\ContactBundle\Entity\ContactsGroupContact;
use AudienceHero\Bundle\CoreBundle\Behavior\Identifiable\IdentifiableEntity;
use AudienceHero\Bundle\CoreBundle\Behavior\Identifiable\IdentifiableInterface;
use AudienceHero\Bundle\CoreBundle\Behavior\Ownable\OwnableEntity;
use AudienceHero\Bundle\CoreBundle\Behavior\Ownable\OwnableInterface;
use AudienceHero\Bundle\CoreBundle\Behavior\Timestampable\TimestampableEntity;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Serializer\Annotation\MaxDepth;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * CampaignRecipient.
 *
 * @author Marc Weistroff <marc@audiencehero.org>
 * TODO: Implements OwnableInterface and OwnableEntity
 */
abstract class CampaignRecipient implements OwnableInterface, IdentifiableInterface
{
    const STATUS_NOT_DELIVERED = 'not_delivered';
    const STATUS_PENDING = 'pending';
    const STATUS_SENT = 'sent';

    use TimestampableEntity;
    use OwnableEntity;
    use \AudienceHero\Bundle\CoreBundle\Behavior\Identifiable\IdentifiableEntity;

    /**
     * @var string
     * @ORM\Column(type="string", length=16, nullable=false)
     * @Assert\Length(max=16)
     */
    protected $status = self::STATUS_PENDING;

    /**
     * @var null|ContactsGroupContact
     * @ORM\ManyToOne(targetEntity="AudienceHero\Bundle\ContactBundle\Entity\ContactsGroupContact", cascade={"persist"})
     * @ORM\JoinColumn(name="contacts_group_contact_id", referencedColumnName="id", onDelete="SET NULL")
     * @MaxDepth(1)
     * @Assert\NotNull()
     * @Assert\Valid()
     */
    protected $contactsGroupContact;

    /**
     * @var null|\DateTime
     * @ORM\Column(type="utc_datetime", nullable=true)
     * @Gedmo\Timestampable(on="change", field="status", value="sent")
     */
    protected $sentAt;

    /**
     * @var null|string
     * @ORM\Column(type="string", length=64, nullable=true)
     * @Assert\Length(max=64)
     */
    protected $salutationName;

    public function getSentAt(): ?\DateTime
    {
        return $this->sentAt;
    }

    public function setSentAt(\DateTime $sentAt): void
    {
        $this->sentAt = $sentAt;
    }

    public function isStatusNotDelivered(): bool
    {
        return self::STATUS_NOT_DELIVERED === $this->getStatus();
    }

    public function isStatusPending(): bool
    {
        return self::STATUS_PENDING === $this->getStatus();
    }

    public function isStatusSent(): bool
    {
        return self::STATUS_SENT === $this->getStatus();
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setContactsGroupContact(ContactsGroupContact $contactsGroupContact): void
    {
        $this->contactsGroupContact = $contactsGroupContact;
    }

    public function getContactsGroupContact(): ?ContactsGroupContact
    {
        return $this->contactsGroupContact;
    }

    public function getSalutationName(): ?string
    {
        return $this->salutationName;
    }

    /**
     * @param string $salutationName
     */
    public function setSalutationName(?string $salutationName): void
    {
        $this->salutationName = $salutationName;
    }
}
