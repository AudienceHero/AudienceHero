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

namespace AudienceHero\Bundle\MailingCampaignBundle\Mailer\Model;

use AudienceHero\Bundle\CoreBundle\Bridge\Sylius\Mailer\Model\IdentifiableEmailInterface;
use AudienceHero\Bundle\CoreBundle\Bridge\Sylius\Mailer\Model\IdentifiableEmailTrait;
use AudienceHero\Bundle\CoreBundle\Bridge\Sylius\Mailer\Model\TaggableEmailInterface;
use AudienceHero\Bundle\CoreBundle\Bridge\Sylius\Mailer\Model\TrackableEmailInterface;
use AudienceHero\Bundle\MailingCampaignBundle\Entity\Mailing;
use AudienceHero\Bundle\MailingCampaignBundle\Entity\MailingRecipient;
use Sylius\Component\Mailer\Model\Email;

/**
 * MailingCampaignEmail.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
class MailingCampaignEmail extends Email implements TaggableEmailInterface, TrackableEmailInterface, IdentifiableEmailInterface
{
    use IdentifiableEmailTrait;

    /** @var Mailing */
    private $mailing;

    /** @var MailingRecipient */
    private $mailingRecipient;

    public function getTags(): array
    {
        return ['mailing_campaign'];
    }

    public function trackClicks(): bool
    {
        return true;
    }

    public function trackOpens(): bool
    {
        return true;
    }

    /**
     * @return MailingRecipient
     */
    public function getMailingRecipient(): MailingRecipient
    {
        return $this->mailingRecipient;
    }

    /**
     * @param MailingRecipient $mailingRecipient
     */
    public function setMailingRecipient(MailingRecipient $mailingRecipient)
    {
        $this->mailingRecipient = $mailingRecipient;
    }

    /**
     * @return Mailing
     */
    public function getMailing(): Mailing
    {
        return $this->mailing;
    }

    /**
     * @param Mailing $mailing
     */
    public function setMailing(Mailing $mailing)
    {
        $this->mailing = $mailing;
    }
}
