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

use ApiPlatform\Core\Annotation\ApiResource;
use AudienceHero\Bundle\ContactBundle\Entity\ContactsGroupContact;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\MaxDepth;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * MailingRecipient.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 *
 * @ORM\Table(name="ah_mailing_recipient")
 * @ORM\Entity(repositoryClass="AudienceHero\Bundle\MailingCampaignBundle\Repository\MailingRecipientRepository")
 * @ApiResource(
 *     attributes={
 *         "normalization_context"={"groups"={"read"}, "enable_max_depth"=true},
 *         "denormalization_context"={"groups"={"write"}}
 *     },
 *     collectionOperations={
 *         "get"={"method"="GET"},
 *     },
 *     itemOperations={
 *         "get"={"method"="GET"},
 *     }
 * )
 */
class MailingRecipient extends CampaignRecipient
{
    const STATUS_OPENED = 'opened';

    /**
     * @var null|Mailing
     * @ORM\ManyToOne(targetEntity="AudienceHero\Bundle\MailingCampaignBundle\Entity\Mailing", inversedBy="recipients")
     * @ORM\JoinColumn(name="mailing_id", referencedColumnName="id", onDelete="CASCADE", nullable=false)
     * @Assert\NotNull()
     * @Assert\Valid()
     * @MaxDepth(1)
     */
    private $mailing;

    /**
     * @var null|MailingRecipient
     * @ORM\OneToOne(targetEntity=MailingRecipient::class, cascade={"all"})
     * @ORM\JoinColumn(name="boost_mailing_recipient_id", referencedColumnName="id", onDelete="SET NULL", nullable=true)
     */
    private $boostMailingRecipient;

    /**
     * @var null|Email
     *
     * @ORM\OneToOne(targetEntity="AudienceHero\Bundle\MailingCampaignBundle\Entity\Email")
     * @ORM\JoinColumn(name="email_id", referencedColumnName="id", onDelete="SET NULL", nullable=true)
     * @MaxDepth(1)
     */
    private $email;

    /**
     * @var null|string
     * @ORM\Column(type="string", length=128, nullable=false)
     * @Assert\NotBlank()
     * @Assert\Length(max=128)
     */
    private $toName;

    /**
     * @var null|string
     * @ORM\Column(type="string", length=255, nullable=false)
     * @Assert\NotBlank()
     * @Assert\Length(max=255)
     */
    private $toEmail;

    /**
     * @var int
     * @ORM\Column(type="integer", nullable=false)
     */
    private $countMailClick = 0;

    /**
     * @var int
     * @ORM\Column(type="integer", nullable=false)
     */
    private $countMailOpen = 0;

    private $isTest = false;

    public function setMailing(Mailing $mailing): void
    {
        $this->mailing = $mailing;
        if (!$this->getOwner()) {
            $this->setOwner($mailing->getOwner());
        }

        if (!$mailing->getRecipients()->contains($this)) {
            $mailing->getRecipients()->add($this);
        }
    }

    public function getMailing(): ?Mailing
    {
        return $this->mailing;
    }

    public function setToEmail(string $toEmail): void
    {
        $this->toEmail = $toEmail;
    }

    public function getToEmail(): ?string
    {
        return $this->toEmail;
    }

    public function setToName(string $toName): void
    {
        $this->toName = $toName;
    }

    public function getToName(): ?string
    {
        return $this->toName;
    }

    /**
     * @param Email $email
     */
    public function setEmail(Email $email): void
    {
        $this->email = $email;
        $email->setMailingRecipient($this);
    }

    /**
     * @return Email
     */
    public function getEmail(): ?Email
    {
        return $this->email;
    }

    public function setCountMailOpen(int $countMailOpen): void
    {
        $this->countMailOpen = $countMailOpen;
    }

    public function setCountMailClick(int $countMailClick): void
    {
        $this->countMailClick = $countMailClick;
    }

    public function incrementMailOpenCounter(): void
    {
        ++$this->countMailOpen;
    }

    public function incrementMailClickCounter(): void
    {
        ++$this->countMailClick;
    }

    public function getMailClickCounter(): int
    {
        return $this->countMailClick;
    }

    public function getMailOpenCounter(): int
    {
        return $this->countMailOpen;
    }

    public function isStatusOpened(): bool
    {
        return self::STATUS_OPENED === $this->getStatus();
    }

    public function setContactsGroupContact(ContactsGroupContact $contactsGroupContact): void
    {
        parent::setContactsGroupContact($contactsGroupContact);
        $contact = $contactsGroupContact->getContact();

        $this->setToName($contact->getName() ?: $contact->getEmail());
        $this->setToEmail($contact->getEmail());
        $this->setSalutationName($contact->getSalutationName());
    }

    /**
     * @return bool
     */
    public function getIsTest(): bool
    {
        return $this->isTest;
    }

    /**
     * @param bool $isTest
     */
    public function setIsTest(bool $isTest)
    {
        $this->isTest = $isTest;
    }

    /**
     * @return mixed
     */
    public function getBoostMailingRecipient(): ?MailingRecipient
    {
        return $this->boostMailingRecipient;
    }

    /**
     * @param mixed $boostMailingRecipient
     */
    public function setBoostMailingRecipient(?MailingRecipient $boostMailingRecipient)
    {
        $this->boostMailingRecipient = $boostMailingRecipient;
    }
}
