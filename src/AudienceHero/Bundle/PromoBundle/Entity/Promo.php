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

namespace AudienceHero\Bundle\PromoBundle\Entity;

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
use AudienceHero\Bundle\CoreBundle\Behavior\Searchable\SearchableEntity;
use AudienceHero\Bundle\CoreBundle\Behavior\Timestampable\TimestampableEntity;
use AudienceHero\Bundle\FileBundle\Entity\File;
use AudienceHero\Bundle\FileBundle\Entity\Player;
use AudienceHero\Bundle\FileBundle\Entity\PlayerTrack;
use AudienceHero\Bundle\MailingCampaignBundle\Entity\Campaign;
use AudienceHero\Bundle\MailingCampaignBundle\Entity\Email;
use AudienceHero\Bundle\MailingCampaignBundle\Entity\Mailing;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use MarcW\Validator\Constraints as MarcWAssert;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\MaxDepth;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Promo.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 * @ORM\Entity(repositoryClass="AudienceHero\Bundle\PromoBundle\Repository\PromoRepository")
 * @ORM\Table(name="ah_promo")
 * @ApiResource(
 *     attributes={
 *         "filters"={"audience_hero.api.order.timestampable"},
 *         "normalization_context"={"groups"={"read"}, "enable_max_depth"=true},
 *         "denormalization_context"={"groups"={"write"}},
 *     },
 *     collectionOperations={
 *         "get"={"method"="GET"},
 *         "post"={"method"="POST"},
 *     },
 *     itemOperations={
 *         "get"={"method"="GET"},
 *         "put"={"method"="PUT"},
 *         "send"={"route_name"="api_promos_send"},
 *         "send_preview"={"route_name"="api_promos_send_preview"},
 *         "feedback"={"route_name"="api_promos_feedback"},
 *         "delete"={"method"="DELETE"},
 *     },
 * )
 */
class Promo implements OwnableInterface, LinkableInterface, ReferenceableInterface, PublishableInterface, IdentifiableInterface
{
    use TimestampableEntity;
    use SearchableEntity;
    use OwnableEntity;
    use LinkableEntity;
    use ReferenceableEntity;
    use PublishableEntity;
    use IdentifiableEntity;

    /**
     * @var null|string
     * @ORM\Column(type="string", length=127, nullable=false)
     * @Groups({"read", "write"})
     * @Assert\NotBlank()
     * @Assert\Length(max=127)
     */
    private $name;

    /**
     * @var null|File
     * @ORM\ManyToOne(targetEntity="AudienceHero\Bundle\FileBundle\Entity\File")
     * @ORM\JoinColumn(name="artwork_id", referencedColumnName="id", onDelete="RESTRICT", nullable=true)
     * @Assert\NotNull()
     * @Groups({"read", "write"})
     * @MaxDepth(1)
     */
    private $artwork;

    /**
     * @var null|string
     * @ORM\Column(type="text", nullable=true)
     * @Assert\NotBlank()
     * @Groups({"read", "write"})
     */
    private $description;

    /**
     * @var null|string
     * @ORM\Column(type="string", length=64, nullable=true)
     * @Assert\Length(max=64)
     * @Groups({"read", "write"})
     */
    private $label;

    /**
     * @var null|string
     * @ORM\Column(type="string", length=32, nullable=true)
     * @Assert\Length(max=32)
     * @Groups({"read", "write"})
     */
    private $catalog;

    /**
     * @var null|string
     * @ORM\Column(type="string", length=64, nullable=true)
     * @Assert\Length(max=64)
     * @Groups({"read", "write"})
     */
    private $genre;

    /**
     * @var null|string
     * @ORM\Column(type="string", length=64, nullable=true)
     * @Assert\Length(max=64)
     * @Groups({"read", "write"})
     */
    private $releaseDate;

    /**
     * @var null|File
     * @ORM\ManyToOne(targetEntity="AudienceHero\Bundle\FileBundle\Entity\File")
     * @ORM\JoinColumn(name="download_id", referencedColumnName="id", onDelete="RESTRICT", nullable=true)
     * @Assert\NotNull()
     * @Groups({"read", "write"})
     * @MaxDepth(1)
     */
    private $download;

    /**
     * @var null|Mailing
     * @ORM\OneToOne(targetEntity="AudienceHero\Bundle\MailingCampaignBundle\Entity\Mailing", cascade={"all"})
     * @ORM\JoinColumn(name="mailing_id", referencedColumnName="id", nullable=true)
     * @Assert\Valid()
     * @Groups({"private_read", "write"})
     */
    private $mailing;

    /**
     * @var Collection
     * @ORM\ManyToOne(targetEntity="AudienceHero\Bundle\FileBundle\Entity\Player", cascade={"persist"})
     * @ORM\JoinColumn(name="player_id", referencedColumnName="id", nullable=true)
     * @Groups({"read", "write", "player"})
     * @MaxDepth(2)
     */
    private $player;

    /**
     * @var Collection
     * @ORM\OneToMany(targetEntity="PromoRecipient", mappedBy="promo")
     * @ORM\OrderBy({"updatedAt":"ASC"})
     */
    private $recipients;

    /**
     * @var null|File
     * @ORM\ManyToOne(targetEntity="AudienceHero\Bundle\FileBundle\Entity\File")
     * @ORM\JoinColumn(name="branding_logo_id", referencedColumnName="id", onDelete="SET NULL", nullable=true)
     * @Groups({"read", "write"})
     */
    private $brandingLogo;

    /**
     * @var null|string
     * @ORM\Column(type="string", length=16, nullable=true)
     * @Groups({"read", "write"})
     * @MarcWAssert\HTMLColor
     */
    private $brandingBackgroundColor = '#333333';

    /**
     * @var null|string
     * @ORM\Column(type="string", length=16, nullable=true)
     * @Groups({"read", "write"})
     * @MarcWAssert\HTMLColor
     */
    private $brandingColor = '#FFFFFF';

    /**
     * @var null|string
     * @Groups({"write"})
     * @Assert\Email()
     */
    private $testRecipient;

    public function __construct()
    {
        $this->recipients = new ArrayCollection();
        $this->setPrivacy(PublishableInterface::PRIVACY_PRIVATE);
    }

    public function setId(string $id): void
    {
        $this->id = $id;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getTracksTitles(): array
    {
        $t = [];
        foreach ($this->getPlayer()->getTracks() as $track) {
            $t[] = $track->getTitle();
        }

        return $t;
    }

    /**
     * @Groups({"private_read"})
     */
    public function getFavoriteTracksDistribution(): array
    {
        $tracks = [];

        foreach ($this->getPlayer()->getTracks() as $track) {
            $tracks[$track->getId()] = 0;
        }

        foreach ($this->getFeedbacks() as $feedback) {
            $id = $feedback->getFavoriteTrack()->getId();

            ++$tracks[$id];
        }

        $data = [];
        foreach ($this->getPlayer()->getTracks() as $track) {
            $data[] = ['track' => $track->getTitle(), 'count' => $tracks[$track->getId()]];
        }

        return $data;
    }

    public function setArtwork(File $artwork): void
    {
        $this->artwork = $artwork;
    }

    public function getArtwork(): ?File
    {
        return $this->artwork;
    }

    public function setReleaseDate($releaseDate): void
    {
        $this->releaseDate = $releaseDate;
    }

    public function getReleaseDate(): ?string
    {
        return $this->releaseDate;
    }

    public function setLabel(string $label): void
    {
        $this->label = $label;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setCatalog(string $catalog): void
    {
        $this->catalog = $catalog;
    }

    public function getCatalog(): ?string
    {
        return $this->catalog;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setGenre(string $genre): void
    {
        $this->genre = $genre;
    }

    public function getGenre(): ?string
    {
        return $this->genre;
    }

    public function setDownload(File $download): void
    {
        $this->download = $download;
    }

    public function getDownload(): ?File
    {
        return $this->download;
    }

    /**
     * @param PromoRecipient $recipient
     */
    public function addRecipient(PromoRecipient $recipient): void
    {
        $this->recipients->add($recipient);
    }

    /**
     * @return ArrayCollection
     */
    public function getRecipients(): Collection
    {
        return $this->recipients;
    }

    public function setRecipients($recipients)
    {
        $this->recipients = $recipients;
    }

    /**
     * @Groups({"private_read"})
     */
    public function getAverageRating(): float
    {
        $c = 0;
        $v = 0;
        foreach ($this->recipients as $pr) {
            if (0 === $pr->getRating()) {
                continue;
            }

            $v += $pr->getRating();
            ++$c;
        }

        if (0 === $c) {
            return 0;
        }

        return (float) $v / (float) $c;
    }

    /**
     * @Groups({"private_read"})
     */
    public function getRatingDistribution(): array
    {
        $distribution = [0, 0, 0, 0, 0];

        foreach ($this->getFeedbacks() as $feedback) {
            $distribution[$feedback->getRating() - 1] += 1;
        }

        $data = [];
        foreach ($distribution as $rating => $count) {
            $data[] = ['rating' => $rating + 1, 'count' => $count, 'label' => str_repeat('*', $rating + 1)];
        }

        return $data;
    }

    /**
     * @return int
     * @Groups({"private_read"})
     */
    public function getRecipientsCount(): int
    {
        return $this->recipients->count();
    }

    public function getRecipientsStatusPendingCount(): int
    {
        return $this->recipients->filter(function ($pr) {
            return $pr->getMailingRecipient() && $pr->getMailingRecipient()->isStatusPending();
        })->count();
    }

    public function getRecipientsStatusSentCount(): int
    {
        return $this->recipients->filter(function ($pr) {
            return $pr->getMailingRecipient() && $pr->getMailingRecipient()->isStatusSent();
        })->count();
    }

    public function getRecipientsStatusOpenedCount(): int
    {
        return $this->recipients->filter(function ($pr) {
            return $pr->getMailingRecipient() && $pr->getMailingRecipient()->isStatusOpened();
        })->count();
    }

    public function getRecipientsStatusNotDeliveredCount(): int
    {
        return $this->recipients->filter(function ($pr) {
            return $pr->getMailingRecipient() && $pr->getMailingRecipient()->isStatusNotDelivered();
        })->count();
    }

    /**
     * @Groups({"private_read"})
     */
    public function getFeedbacks(): Collection
    {
        return $this->recipients->filter(function ($pr) {
            if ($pr->getFeedback()) {
                return true;
            }
        });
    }

    /**
     * @Groups({"private_read"})
     */
    public function getFeedbacksCount(): int
    {
        return $this->recipients->filter(function ($pr) {
            if ($pr->getFeedback()) {
                return true;
            }
        })->count();
    }

    public function countRecipientsDownloads(): int
    {
        return $this->recipients->filter(function ($pr) {
            return $pr->getCountDownload() > 0;
        })->count();
    }

    public function getFavoriteTrack(): PlayerTrack
    {
        /** @var []PlayerTrack $favoriteTracks */
        $favoriteTracks = [];
        foreach ($this->getTracks() as $track) {
            $favoriteTracks[$track->getTitle()] = 0;
        }

        foreach ($this->getRecipients() as $recipient) {
            if (!$recipient->getFavoriteTrack()) {
                continue;
            }

            ++$favoriteTracks[$recipient->getFavoriteTrack()->getTitle()];
        }

        asort($favoriteTracks);

        return array_keys($favoriteTracks)[0];
    }

    public function setMailing(Mailing $mailing): void
    {
        $this->mailing = $mailing;

        if ($this->getOwner()) {
            $this->mailing->setOwner($this->getOwner());
        }
    }

    public function getMailing(): ?Mailing
    {
        return $this->mailing;
    }

    /**
     * @param Email $email
     *
     * @throws \LogicException
     *
     * @return PromoRecipient|null
     */
    public function findPromoRecipientByEmail(Email $email): ?PromoRecipient
    {
        $filters = $this->getRecipients()->filter(function ($pr) use ($email) {
            return $pr->getMailingRecipient() && $pr->getMailingRecipient()->getContactsGroupContact()->getContact()->getEmail() === $email;
        });

        if (0 === count($filters)) {
            return null;
        }

        if (count($filters) > 1) {
            throw new \LogicException(sprintf('More than one contact has the same email. %s', $email));
        }

        return $filters->first();
    }

    public function setBrandingLogo(?File $brandingLogo): void
    {
        $this->brandingLogo = $brandingLogo;
    }

    public function getBrandingLogo(): ?File
    {
        return $this->brandingLogo;
    }

    public function setBrandingBackgroundColor(string $brandingBackgroundColor): void
    {
        $this->brandingBackgroundColor = $brandingBackgroundColor;
    }

    public function getBrandingBackgroundColor(): ?string
    {
        return $this->brandingBackgroundColor;
    }

    public function setBrandingColor($brandingColor): void
    {
        $this->brandingColor = $brandingColor;
    }

    public function getBrandingColor(): ?string
    {
        return $this->brandingColor;
    }

    public function replicate(): Promo
    {
        $that = clone $this;
        $that->setMailing(clone $this->getMailing());
        $that->getMailing()->setStatus(Campaign::STATUS_DRAFT);
        $now = new \DateTime();
        $that->setCreatedAt($now);
        $that->setUpdatedAt($now);
        $that->getMailing()->setCreatedAt($now);
        $that->getMailing()->setUpdatedAt($now);
        $that->getMailing()->setReminderMailing(null);
        $that->getMailing()->setDeliveredAt(null);
        $that->setPlayer($this->getPlayer());
    }

    /**
     * @return Collection
     */
    public function getPlayer(): ?Player
    {
        return $this->player;
    }

    /**
     * @param null|Player $player
     */
    public function setPlayer(?Player $player): void
    {
        $this->player = $player;
    }

    /**
     * @return null|string
     */
    public function getTestRecipient(): ?string
    {
        return $this->testRecipient;
    }

    /**
     * @param null|string $testRecipient
     */
    public function setTestRecipient(?string $testRecipient)
    {
        $this->testRecipient = $testRecipient;
    }
}
