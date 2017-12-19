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

use AudienceHero\Bundle\CoreBundle\Behavior\Identifiable\IdentifiableEntity;
use AudienceHero\Bundle\CoreBundle\Behavior\Identifiable\IdentifiableInterface;
use AudienceHero\Bundle\CoreBundle\Behavior\Ownable\OwnableEntity;
use AudienceHero\Bundle\CoreBundle\Behavior\Ownable\OwnableInterface;
use AudienceHero\Bundle\CoreBundle\Behavior\Timestampable\TimestampableEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Email.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 * @ORM\Entity(repositoryClass="AudienceHero\Bundle\MailingCampaignBundle\Repository\EmailRepository")
 * @ORM\Table(name="ah_email", indexes={@ORM\Index(columns={"mandrill_id"})})
 */
class Email implements OwnableInterface, \AudienceHero\Bundle\CoreBundle\Behavior\Identifiable\IdentifiableInterface
{
    use TimestampableEntity;
    use OwnableEntity;
    use \AudienceHero\Bundle\CoreBundle\Behavior\Identifiable\IdentifiableEntity;

    /**
     * @var null|string
     * @ORM\Column(type="string", length=255, nullable=false)
     * @Assert\Length(max=255)
     */
    private $mandrillId;

    /**
     * @var null|MailingRecipient
     * @ORM\ManyToOne(targetEntity="AudienceHero\Bundle\MailingCampaignBundle\Entity\MailingRecipient")
     * @ORM\JoinColumn(name="mailing_recipient_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $mailingRecipient;

    /**
     * @var Collection
     * @ORM\OneToMany(targetEntity="AudienceHero\Bundle\MailingCampaignBundle\Entity\EmailEvent", mappedBy="email")
     * @ORM\OrderBy({"createdAt"="ASC"})
     */
    private $events;

    public function __construct()
    {
        $this->events = new ArrayCollection();
    }

    public function setMandrillId(string $mandrillId): void
    {
        $this->mandrillId = $mandrillId;
    }

    public function getMandrillId(): ?string
    {
        return $this->mandrillId;
    }

    public function addEvent(EmailEvent $event): void
    {
        $this->events->add($event);
        $event->setEmail($this);
    }

    public function setEvents($events): void
    {
        $this->events = $events;
    }

    /**
     * @return Collection
     */
    public function getEvents(): Collection
    {
        return $this->events;
    }

    public function setMailingRecipient(MailingRecipient $mailingRecipient): void
    {
        $this->mailingRecipient = $mailingRecipient;
    }

    public function getMailingRecipient(): ?MailingRecipient
    {
        return $this->mailingRecipient;
    }
}
