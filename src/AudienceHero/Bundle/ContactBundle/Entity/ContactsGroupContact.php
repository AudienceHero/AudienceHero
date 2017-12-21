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
use AudienceHero\Bundle\CoreBundle\Behavior\Timestampable\TimestampableEntity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\MaxDepth;

/**
 * ContactsGroupContact.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 * @ORM\Entity(repositoryClass="AudienceHero\Bundle\ContactBundle\Repository\ContactsGroupContactRepository")
 * @ORM\Table(name="ah_contacts_group_contact", uniqueConstraints={@ORM\UniqueConstraint(name="idx_unique_contact_groups_contact_group", columns={"contact_id", "group_id"})})
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
class ContactsGroupContact implements \AudienceHero\Bundle\CoreBundle\Behavior\Ownable\OwnableInterface, IdentifiableInterface
{
    use TimestampableEntity;
    use OwnableEntity;
    use IdentifiableEntity;

    const CLEAN_REASON_SPAM = 'spam';
    const CLEAN_REASON_HARD_BOUNCE = 'hard_bounce';

    /**
     * @var string|null
     * @Groups({"read"})
     * @ORM\Column(type="string", length=16, nullable=true)
     */
    private $cleanedReason;

    /**
     * @var null|\AudienceHero\Bundle\ContactBundle\Entity\ContactsGroup
     * @ORM\ManyToOne(targetEntity="AudienceHero\Bundle\ContactBundle\Entity\ContactsGroup", inversedBy="contacts")
     * @ORM\JoinColumn(name="group_id", referencedColumnName="id", onDelete="CASCADE", nullable=false)
     * @Groups({"contact.read", "write"})
     * @MaxDepth(1)
     */
    private $group;

    /**
     * @var null|Contact
     * @ORM\ManyToOne(targetEntity="AudienceHero\Bundle\ContactBundle\Entity\Contact", inversedBy="groups")
     * @ORM\JoinColumn(name="contact_id", referencedColumnName="id", onDelete="CASCADE", nullable=false)
     */
    private $contact;

    /**
     * @var null|\DateTime
     * @ORM\Column(type="datetime", nullable=true)
     * @Groups({"read", "contact.read"})
     */
    private $optinAt;

    /**
     * @var \DateTime|null
     * @ORM\Column(type="datetime", nullable=true)
     * @Groups({"read", "contact.read"})
     */
    private $unsubscribedAt;

    /**
     * @var \DateTime|null
     * @ORM\Column(type="datetime", nullable=true)
     * @Groups({"read", "contact.read"})
     */
    private $cleanedAt;

    public function acceptEmails(): bool
    {
        if (!$this->isOptin()) {
            return false;
        }

        if ($this->isUnsubscribed()) {
            return false;
        }

        if ($this->isCleaned()) {
            return false;
        }

        return true;
    }

    public function setContact(?Contact $contact): void
    {
        $this->contact = $contact;
        if (!$this->getOwner()) {
            $this->setOwner($contact->getOwner());
        }
    }

    public function getContact(): ?Contact
    {
        return $this->contact;
    }

    public function setGroup(?ContactsGroup $group): void
    {
        $this->group = $group;
    }

    public function getGroup(): ?ContactsGroup
    {
        return $this->group;
    }

    public function unsubscribe(): void
    {
        $this->setUnsubscribedAt(new \DateTime());
    }

    public function isOptin(): bool
    {
        return null !== $this->optinAt;
    }

    public function isSubscribed(): bool
    {
        return null === $this->unsubscribedAt;
    }

    public function isUnsubscribed(): bool
    {
        return null !== $this->unsubscribedAt;
    }

    public function setOptinAt(\DateTime $optinAt): void
    {
        $this->optinAt = $optinAt;
    }

    public function getOptinAt(): ?\DateTime
    {
        return $this->optinAt;
    }

    public function setCleanedAt(\DateTime $cleanedAt): void
    {
        $this->cleanedAt = $cleanedAt;
    }

    public function isCleaned(): bool
    {
        return null !== $this->cleanedAt;
    }

    public function getCleanedAt(): ?\DateTime
    {
        return $this->cleanedAt;
    }

    public function setCleanedReason(string $cleanedReason): void
    {
        $this->cleanedReason = $cleanedReason;
    }

    public function getCleanedReason(): ?string
    {
        return $this->cleanedReason;
    }

    public function setUnsubscribedAt(\DateTime $unsubscribedAt): void
    {
        $this->unsubscribedAt = $unsubscribedAt;
    }

    public function getUnsubscribedAt(): ?\DateTime
    {
        return $this->unsubscribedAt;
    }

    public function resubscribe(): void
    {
        $this->unsubscribedAt = null;
    }

    public function removeCleanState(): void
    {
        $this->cleanedAt = null;
        $this->cleanedReason = null;
    }
}
