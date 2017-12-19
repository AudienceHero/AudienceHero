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

use AudienceHero\Bundle\ContactBundle\Entity\ContactsGroupContact;
use AudienceHero\Bundle\MailingCampaignBundle\Entity\Mailing;
use AudienceHero\Bundle\MailingCampaignBundle\Entity\MailingRecipient;

/**
 * MailingRecipientFactory.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
class MailingRecipientFactory
{
    public function create(Mailing $mailing, ContactsGroupContact $cgc): MailingRecipient
    {
        $contact = $cgc->getContact();

        $mr = new MailingRecipient();
        $mr->setMailing($mailing);
        $mr->setOwner($mailing->getOwner());
        $mr->setContactsGroupContact($cgc);

        return $mr;
    }
}
