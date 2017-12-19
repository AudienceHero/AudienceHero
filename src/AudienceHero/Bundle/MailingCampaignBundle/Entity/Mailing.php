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
use AudienceHero\Bundle\CoreBundle\Behavior\Ownable\OwnableEntity;
use AudienceHero\Bundle\CoreBundle\Entity\PersonEmail;
use AudienceHero\Bundle\CoreBundle\Behavior\Referenceable\ReferenceableEntity;
use AudienceHero\Bundle\CoreBundle\Behavior\Referenceable\ReferenceableInterface;
use AudienceHero\Bundle\CoreBundle\Behavior\Searchable\SearchableEntity;
use AudienceHero\Bundle\CoreBundle\Behavior\Timestampable\TimestampableEntity;
use AudienceHero\Bundle\CoreBundle\Validator\Constraints\PersonEmailVerified;
use AudienceHero\Bundle\FileBundle\Entity\File;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Mailing.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 * @ORM\Entity(repositoryClass="AudienceHero\Bundle\MailingCampaignBundle\Repository\MailingRepository")
 * @ORM\Table(name="ah_mailing")
 * @ApiResource(
 *     attributes={
 *         "filters"={
 *             "audience_hero.api.order.timestampable",
 *             "audience_hero.mailing_campaign.order",
 *             "audience_hero.mailing_campaign.boolean",
 *         },
 *         "normalization_context"={"groups"={"read"}, "enable_max_depth"=true},
 *         "denormalization_context"={"groups"={"write"}}
 *     },
 *     collectionOperations={
 *         "get"={"method"="GET"},
 *         "post"={"method"="POST"}
 *     },
 *     itemOperations={
 *         "get"={"method"="GET", "normalization_context"={"groups"={"read", "metrics"}}},
 *         "send"={"route_name"="api_mailings_send"},
 *         "send_preview"={"route_name"="api_mailings_send_preview"},
 *         "put"={"method"="PUT"},
 *         "boost"={"route_name"="api_mailings_boost"},
 *     }
 * )
 */
class Mailing extends Campaign implements \AudienceHero\Bundle\CoreBundle\Behavior\Referenceable\ReferenceableInterface
{
    use TimestampableEntity;
    use OwnableEntity;
    use \AudienceHero\Bundle\CoreBundle\Behavior\Searchable\SearchableEntity;
    use \AudienceHero\Bundle\CoreBundle\Behavior\Referenceable\ReferenceableEntity;

    /**
     * @var bool
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $isInternal = false;

    /**
     * @var Mailing
     * @ORM\OneToOne(targetEntity="AudienceHero\Bundle\MailingCampaignBundle\Entity\Mailing", cascade={"persist"})
     * @ORM\JoinColumn(name="boost_mailing_id", referencedColumnName="id", onDelete="SET NULL")
     * @Groups({"read", "write"})
     */
    private $boostMailing;

    /**
     * @var null|PersonEmail
     * @ORM\ManyToOne(targetEntity="AudienceHero\Bundle\CoreBundle\Entity\PersonEmail")
     * @ORM\JoinColumn(name="person_email_id", referencedColumnName="id", nullable=true, onDelete="RESTRICT")
     * @Assert\NotNull()
     * @PersonEmailVerified()
     * @Groups({"read", "write"})
     */
    private $personEmail;

    /**
     * @var null|string
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\NotBlank(groups={"mailing_step2", "promo_step2"})
     * @Assert\Length(max=255)
     * @Groups({"read", "write"})
     */
    private $fromName;

    /**
     * @var null|string
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\NotBlank(groups={"mailing_step2", "promo_step2"})
     * @Assert\Length(max=255)
     * @Groups({"read", "write"})
     */
    private $subject;

    /**
     * @var null|string
     * @ORM\Column(type="text", nullable=true)
     * @Assert\NotBlank(groups={"mailing_step2"})
     * @Groups({"read", "write"})
     */
    private $body;

    /**
     * @var null|File
     * @ORM\ManyToOne(targetEntity="AudienceHero\Bundle\FileBundle\Entity\File")
     * @ORM\JoinColumn(name="artwork_id", referencedColumnName="id", onDelete="SET NULL", nullable=true)
     * @Groups({"read", "write"})
     */
    private $artwork;

    /**
     * @var null|\DateTime
     * @ORM\Column(type="utc_datetime", nullable=true)
     * @Gedmo\Timestampable(on="change", field="status", value="delivered")
     * @Groups({"read"})
     */
    private $deliveredAt;

    /**
     * @var Collection
     * @ORM\OneToMany(targetEntity="AudienceHero\Bundle\MailingCampaignBundle\Entity\MailingRecipient", mappedBy="mailing", cascade={"all"})
     * @ORM\OrderBy({"updatedAt"="DESC"})
     */
    private $recipients;

    /**
     * Not persisted. Used by the API.
     *
     * @var null|string
     * @Groups({"read", "write"})
     * @Assert\Email()
     */
    private $testRecipient;

    public function __construct()
    {
        $this->recipients = new ArrayCollection();
    }

    public function setFromName(string $fromName): void
    {
        $this->fromName = $fromName;
    }

    public function getFromName(): ?string
    {
        return $this->fromName;
    }

    public function setSubject(?string $subject): void
    {
        $this->subject = $subject;
    }

    public function getSubject(): ?string
    {
        return $this->subject;
    }

    public function setBody(?string $body): void
    {
        $this->body = $body;
    }

    public function getBody(): ?string
    {
        return $this->body;
    }

    public function setDeliveredAt(\DateTime $deliveredAt = null): void
    {
        $this->deliveredAt = $deliveredAt;
    }

    public function getDeliveredAt(): ?\DateTime
    {
        return $this->deliveredAt;
    }

    /**
     * @return Collection
     */
    public function getRecipients(): Collection
    {
        return $this->recipients;
    }

    /**
     * @param array|ArrayCollection $recipients
     */
    public function setRecipients($recipients): void
    {
        if ($recipients instanceof ArrayCollection) {
            $this->recipients = $recipients;

            return;
        }

        if (is_array($recipients)) {
            $this->recipients = new ArrayCollection($recipients);

            return;
        }

        throw new \LogicException('Recipients should be an array or an ArrayCollection');
    }

    /**
     * @Groups({"metrics"})
     */
    public function getRateOpen(): float
    {
        if (0 === $this->getRecipients()->count()) {
            return 0;
        }

        return ($this->getCountUniqueOpen() / $this->getRecipients()->count()) * 100;
    }

    /**
     * @Groups({"metrics"})
     */
    public function getRateClick(): float
    {
        if (0 === $this->getRecipients()->count()) {
            return 0;
        }

        return ($this->getCountUniqueClick() / $this->getRecipients()->count()) * 100;
    }

    /**
     * @Groups({"metrics"})
     */
    public function getRateDelivery(): float
    {
        if (0 === $this->getRecipients()->count()) {
            return 0;
        }

        return 100 - ($this->getCountNonDelivered() / $this->getRecipients()->count()) * 100;
    }

    /**
     * @Groups({"metrics"})
     */
    public function getCountUniqueOpen(): int
    {
        $count = 0;
        foreach ($this->getRecipients() as $recipient) {
            if ($recipient->getMailOpenCounter() > 0) {
                ++$count;
            }
        }

        return $count;
    }

    /**
     * @Groups({"metrics"})
     */
    public function getCountUniqueClick(): int
    {
        $count = 0;
        foreach ($this->getRecipients() as $recipient) {
            if ($recipient->getMailClickCounter() > 0) {
                ++$count;
            }
        }

        return $count;
    }

    /**
     * @Groups({"metrics"})
     */
    public function getCountTotalOpens(): int
    {
        $count = 0;
        foreach ($this->getRecipients() as $recipient) {
            $count += $recipient->getMailOpenCounter();
        }

        return $count;
    }

    /**
     * @Groups({"metrics"})
     */
    public function getCountTotalClicks(): int
    {
        $count = 0;
        foreach ($this->getRecipients() as $recipient) {
            $count += $recipient->getMailClickCounter();
        }

        return $count;
    }

    /**
     * @Groups({"metrics"})
     */
    public function getRateClickByUniqueOpen(): float
    {
        if ($this->getCountUniqueOpen() <= 0) {
            return 0.0;
        }

        return ($this->getCountUniqueClick() / $this->getCountUniqueOpen()) * 100;
    }

    /**
     * @Groups({"metrics"})
     */
    public function getCountDelivered(): int
    {
        $count = 0;
        foreach ($this->getRecipients() as $recipient) {
            if ($recipient->isStatusPending()) {
                continue;
            }
            if ($recipient->isStatusNotDelivered()) {
                continue;
            }
            ++$count;
        }

        return $count;
    }

    /**
     * @Groups({"metrics"})
     */
    public function getCountNonDelivered(): int
    {
        $count = 0;
        foreach ($this->getRecipients() as $recipient) {
            if ($recipient->isStatusNotDelivered()) {
                ++$count;
            }
        }

        return $count;
    }

    public function setArtwork(?File $artwork): void
    {
        $this->artwork = $artwork;
    }

    public function getArtwork(): ?File
    {
        return $this->artwork;
    }

    public function setBoostMailing(Mailing $boostMailing): void
    {
        $this->boostMailing = $boostMailing;
    }

    public function getBoostMailing(): ?Mailing
    {
        return $this->boostMailing;
    }

    public function setIsInternal(bool $isInternal): void
    {
        $this->isInternal = $isInternal;
    }

    public function getIsInternal(): bool
    {
        return $this->isInternal;
    }

    /**
     * @return null|PersonEmail
     */
    public function getPersonEmail(): ?PersonEmail
    {
        return $this->personEmail;
    }

    /**
     * @param null|PersonEmail $personEmail
     */
    public function setPersonEmail(?PersonEmail $personEmail): void
    {
        $this->personEmail = $personEmail;
    }

    /**
     * @return null|string
     */
    public function getTestRecipient()
    {
        return $this->testRecipient;
    }

    /**
     * @param null|string $testRecipient
     */
    public function setTestRecipient($testRecipient)
    {
        $this->testRecipient = $testRecipient;
    }
}
