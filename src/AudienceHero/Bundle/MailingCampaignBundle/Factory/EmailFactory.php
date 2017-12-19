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

namespace AudienceHero\Bundle\MailingCampaignBundle\Factory;

use AudienceHero\Bundle\MailingCampaignBundle\Entity\Email;
use AudienceHero\Bundle\MailingCampaignBundle\Entity\MailingRecipient;

/**
 * EmailFactory.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
class EmailFactory
{
    public function create(MailingRecipient $mr): Email
    {
        $email = new Email();
        $email->setMailingRecipient($mr);
        $email->setOwner($mr->getOwner());
        $mr->setEmail($email);

        return $email;
    }
}
