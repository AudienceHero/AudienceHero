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

namespace AudienceHero\Bundle\MailingCampaignBundle\Mailer;

use AudienceHero\Bundle\MailingCampaignBundle\Entity\Mailing;
use AudienceHero\Bundle\MailingCampaignBundle\Entity\MailingRecipient;

/**
 * MailerInterface.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
interface MailerInterface
{
    public function send(MailingRecipient $recipient);

    public function sendPreview(Mailing $mailing, string $to);
}
