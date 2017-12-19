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
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Email.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 * @ORM\Entity(repositoryClass="AudienceHero\Bundle\MailingCampaignBundle\Repository\EmailEventRepository")
 * @ORM\Table(name="ah_email_event")
 */
class EmailEvent implements \AudienceHero\Bundle\CoreBundle\Behavior\Identifiable\IdentifiableInterface, \AudienceHero\Bundle\CoreBundle\Behavior\Ownable\OwnableInterface
{
    use \AudienceHero\Bundle\CoreBundle\Behavior\Timestampable\TimestampableEntity;
    use IdentifiableEntity;
    use \AudienceHero\Bundle\CoreBundle\Behavior\Ownable\OwnableEntity;

    const EVENT_CLICK = 'email.click';
    const EVENT_DEFERRAL = 'email.deferral';
    const EVENT_HARD_BOUNCE = 'email.hard_bounce';
    const EVENT_OPEN = 'email.open';
    const EVENT_REJECT = 'email.reject';
    const EVENT_SEND = 'email.send';
    const EVENT_SOFT_BOUNCE = 'email.soft_bounce';
    const EVENT_SPAM = 'email.spam';
    const EVENT_UNSUB = 'email.unsub';

    /**
     * @var null|Email
     * @ORM\ManyToOne(targetEntity="AudienceHero\Bundle\MailingCampaignBundle\Entity\Email", inversedBy="events")
     * @ORM\JoinColumn(name="email_id", referencedColumnName="id", onDelete="CASCADE", nullable=false)
     */
    private $email;

    /**
     * @var null|string
     * @ORM\Column(type="string", length=64, nullable=false)
     * @Assert\Length(max=64)
     */
    private $event;

    /**
     * @var null|string
     * @ORM\Column(type="string", length=127, nullable=true)
     * @Assert\Length(max=127)
     */
    private $ip;

    /**
     * @var array
     * @ORM\Column(type="json_array", nullable=true)
     */
    private $data = [];

    public function setEmail(Email $email): void
    {
        $this->email = $email;
        $this->setOwner($email->getOwner());
    }

    public function getEmail(): ?Email
    {
        return $this->email;
    }

    public function setData(array $data): void
    {
        $this->data = $data;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function setIp(string $ip)
    {
        $this->ip = $ip;
    }

    public function getIp(): ?string
    {
        return $this->ip;
    }

    public function isClick(): bool
    {
        return self::EVENT_CLICK === $this->event;
    }

    public function isOpen(): bool
    {
        return self::EVENT_OPEN === $this->event;
    }

    public function isSend(): bool
    {
        return self::EVENT_SEND === $this->event;
    }

    public function isBounce(): bool
    {
        return $this->isHardBounce() || self::EVENT_SOFT_BOUNCE === $this->event;
    }

    public function isHardBounce(): bool
    {
        return self::EVENT_HARD_BOUNCE === $this->event;
    }

    public function isReject(): bool
    {
        return self::EVENT_REJECT === $this->event;
    }

    public function isSpam(): bool
    {
        return self::EVENT_SPAM === $this->event;
    }

    public function setEvent(string $event): void
    {
        $this->event = $event;
    }

    public function getEvent(): ?string
    {
        return $this->event;
    }
}
