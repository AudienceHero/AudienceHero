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
use AudienceHero\Bundle\CoreBundle\Behavior\Timestampable\TimestampableEntity;
use AudienceHero\Bundle\FileBundle\Entity\PlayerTrack;
use AudienceHero\Bundle\MailingCampaignBundle\Entity\MailingRecipient;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * PromoRecipient.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 *
 * @ORM\Table(name="ah_promo_recipient")
 * @ORM\Entity(repositoryClass="AudienceHero\Bundle\PromoBundle\Repository\PromoRecipientRepository")
 * @ApiResource(
 *     attributes={
 *         "normalization_context"={"groups"={"read"}, "enable_max_depth"=true},
 *         "denormalization_context"={"groups"={"write"}},
 *     },
 *     collectionOperations={
 *         "get"={"method"="GET"},
 *     },
 *     itemOperations={
 *         "get"={"method"="GET"},
 *         "put"={"method"="PUT"},
 *     },
 * )
 */
class PromoRecipient implements OwnableInterface, LinkableInterface, IdentifiableInterface
{
    use TimestampableEntity;
    use OwnableEntity;
    use LinkableEntity;
    use IdentifiableEntity;

    /**
     * @var null|Promo
     * @ORM\ManyToOne(targetEntity="Promo", inversedBy="recipients")
     * @ORM\JoinColumn(name="promo_id", referencedColumnName="id", onDelete="CASCADE", nullable=false)
     * @Assert\NotNull()
     * @Assert\Valid()
     * @Groups({"write"})
     */
    private $promo;

    /**
     * @var null|string
     * @ORM\Column(type="string", nullable=true)
     */
    private $name;

    /**
     * @var null|MailingRecipient
     * @ORM\ManyToOne(targetEntity="AudienceHero\Bundle\MailingCampaignBundle\Entity\MailingRecipient")
     * @ORM\JoinColumn(name="mailing_recipient_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $mailingRecipient;

    /**
     * @var null|MailingRecipient
     * @ORM\ManyToOne(targetEntity="AudienceHero\Bundle\MailingCampaignBundle\Entity\MailingRecipient", cascade={"all"})
     * @ORM\JoinColumn(name="boost_mailing_recipient_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $boostMailingRecipient;

    /**
     * @var null|PlayerTrack
     * @Assert\NotBlank(groups={"feedback"})
     * @ORM\ManyToOne(targetEntity="AudienceHero\Bundle\FileBundle\Entity\PlayerTrack")
     * @ORM\JoinColumn(name="favorite_track_id", referencedColumnName="id", onDelete="SET NULL", nullable=true)
     * @Groups({"private_read", "write"})
     */
    private $favoriteTrack;

    /**
     * @var null|number
     * @ORM\Column(type="integer", nullable=true)
     * @Assert\NotBlank(groups={"feedback"})
     * @Assert\Choice(choices={1,2,3,4,5})
     * @Groups({"private_read", "write"})
     */
    private $rating;

    /**
     * @var null|string
     * @ORM\Column(type="text", nullable=true)
     * @Assert\NotBlank(groups={"feedback"})
     * @Groups({"private_read", "write"})
     */
    private $feedback;

    /**
     * @var int
     * @ORM\Column(type="integer", nullable=false)
     */
    private $countVisit = 0;

    /**
     * @var int
     * @ORM\Column(type="integer", nullable=false)
     */
    private $countDownload = 0;

    /**
     * @var bool
     */
    private $isTest = false;

    public function setId(string $id): void
    {
        $this->id = $id;
    }

    public function setPromo(?Promo $promo): void
    {
        $this->promo = $promo;
        $this->setOwner($promo->getOwner());

        if ($promo && !$promo->getRecipients()->contains($this)) {
            $this->promo->addRecipient($this);
        }
    }

    public function getPromo(): ?Promo
    {
        return $this->promo;
    }

    public function setTest(bool $test): void
    {
        $this->isTest = $test;
    }

    public function isTest(): bool
    {
        return $this->isTest;
    }

    public function setFavoriteTrack($favoriteTrack): void
    {
        $this->favoriteTrack = $favoriteTrack;
    }

    /**
     * @return PlayerTrack
     */
    public function getFavoriteTrack(): PlayerTrack
    {
        return $this->favoriteTrack;
    }

    /**
     * @param int $rating
     */
    public function setRating($rating): void
    {
        $this->rating = $rating;
    }

    /**
     * @return int
     */
    public function getRating(): ?int
    {
        return $this->rating;
    }

    /**
     * @param string $feedback
     */
    public function setFeedback(string $feedback): void
    {
        $this->feedback = $feedback;
    }

    /**
     * @return string
     */
    public function getFeedback(): ?string
    {
        return $this->feedback;
    }

    /**
     * @return int
     */
    public function getVisitCounter(): int
    {
        return $this->countVisit;
    }

    /**
     * @return int
     */
    public function getDownloadCounter(): int
    {
        return $this->countDownload;
    }

    public function incrementVisitCounter(): void
    {
        ++$this->countVisit;
    }

    public function incrementDownloadCounter(): void
    {
        ++$this->countDownload;
    }

    /**
     * @return int
     */
    public function getCountDownload(): int
    {
        return $this->countDownload;
    }

    public function setMailingRecipient(?MailingRecipient $mailingRecipient): void
    {
        $this->mailingRecipient = $mailingRecipient;
    }

    /**
     * @return MailingRecipient
     */
    public function getMailingRecipient(): MailingRecipient
    {
        return $this->mailingRecipient;
    }

    /**
     * @return string
     */
    public function getName(): ?string
    {
        if ($this->mailingRecipient) {
            return $this->mailingRecipient->getToName();
        }

        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return MailingRecipient|null
     */
    public function getBoostMailingRecipient(): ?MailingRecipient
    {
        return $this->boostMailingRecipient;
    }

    /**
     * @param MailingRecipient|null $boostMailingRecipient
     */
    public function setBoostMailingRecipient(?MailingRecipient $boostMailingRecipient)
    {
        $this->boostMailingRecipient = $boostMailingRecipient;
    }

    /**
     * @param int $countVisit
     */
    public function setCountVisit(int $countVisit): void
    {
        $this->countVisit = $countVisit;
    }

    /**
     * @param int $countDownload
     */
    public function setCountDownload(int $countDownload): void
    {
        $this->countDownload = $countDownload;
    }

    /**
     * @Groups({"private_read"})
     */
    public function getContact()
    {
        return $this->getMailingRecipient()->getContactsGroupContact()->getContact();
    }
}
