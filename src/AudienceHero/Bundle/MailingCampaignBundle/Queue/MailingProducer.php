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

use AudienceHero\Bundle\CoreBundle\Queue\Producer;
use AudienceHero\Bundle\MailingCampaignBundle\Entity\Mailing;
use AudienceHero\Bundle\MailingCampaignBundle\Entity\MailingRecipient;

/**
 * Producer.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
class MailingProducer
{
    const MAILING_SEND = 'ah.mailing.send';
    const MAILING_RECIPIENT_SEND = 'ah.mailing.recipient.send';

    /**
     * @var Producer
     */
    private $producer;

    public function __construct(Producer $producer)
    {
        $this->producer = $producer;
    }

    public function sendMailing(Mailing $mailing)
    {
        $this->producer->sendCommand(self::MAILING_SEND, MailingMessage::create()->setMailing($mailing)->setIsBoost(false));
    }

    public function sendMailingRecipient(MailingRecipient $mr)
    {
        $this->producer->sendCommand(self::MAILING_RECIPIENT_SEND, MailingMessage::create()->setMailing($mr->getMailing())->setMailingRecipient($mr));
    }

    public function boostMailing(Mailing $mailing)
    {
        $this->producer->sendCommand(self::MAILING_SEND, MailingMessage::create()->setMailing($mailing)->setIsBoost(true));
    }
}
