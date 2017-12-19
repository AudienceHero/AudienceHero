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

namespace AudienceHero\Bundle\MailingCampaignBundle\Queue;

use AudienceHero\Bundle\CoreBundle\Queue\Message;
use AudienceHero\Bundle\MailingCampaignBundle\Entity\Mailing;
use AudienceHero\Bundle\MailingCampaignBundle\Entity\MailingRecipient;

/**
 * MailingMessage.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
class MailingMessage extends Message
{
    /**
     * @var Mailing
     */
    private $mailing;

    /**
     * @var MailingRecipient|null
     */
    private $mailingRecipient;

    /** @var bool */
    private $isBoost = false;

    /**
     * @return Mailing
     */
    public function getMailing(): ?Mailing
    {
        return $this->mailing;
    }

    /**
     * @return MailingRecipient|null
     */
    public function getMailingRecipient(): ?MailingRecipient
    {
        return $this->mailingRecipient;
    }

    /**
     * @param Mailing $mailing
     */
    public function setMailing(Mailing $mailing)
    {
        $this->mailing = $mailing;

        return $this;
    }

    /**
     * @param MailingRecipient|null $mailingRecipient
     */
    public function setMailingRecipient(?MailingRecipient $mailingRecipient)
    {
        $this->mailingRecipient = $mailingRecipient;

        return $this;
    }

    /**
     * @return mixed
     */
    public function isBoost(): bool
    {
        return $this->isBoost;
    }

    /**
     * @param mixed $isBoost
     */
    public function setIsBoost(bool $isBoost)
    {
        $this->isBoost = $isBoost;

        return $this;
    }
}
