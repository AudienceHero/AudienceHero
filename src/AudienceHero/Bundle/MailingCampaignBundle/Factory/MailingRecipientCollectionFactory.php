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
use Webmozart\Assert\Assert;

/**
 * MailingRecipientCollectionFactory.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
class MailingRecipientCollectionFactory
{
    /**
     * @var MailingRecipientFactory
     */
    private $factory;

    public function __construct(MailingRecipientFactory $factory)
    {
        $this->factory = $factory;
    }

    public function createCollection(Mailing $mailing): array
    {
        $group = $mailing->getContactsGroup();

        $mrs = [];
        foreach ($group->getContacts() as $cgc) {
            if (!$this->doesContactsGroupContactAcceptEmail($cgc)) {
                continue;
            }

            $mrs[] = $this->factory->create($mailing, $cgc);
        }

        return $mrs;
    }

    public function createBoostCollection(Mailing $mailing): array
    {
        Assert::notNull($mailing->getBoostMailing(), 'You need to provide the parent instance of Mailing, which contains the Boost mailing.');
        $mrs = [];

        foreach ($mailing->getRecipients() as $recipient) {
            if ($recipient->isStatusOpened()) {
                continue;
            }

            $cgc = $recipient->getContactsGroupContact();
            if (!$this->doesContactsGroupContactAcceptEmail($cgc)) {
                continue;
            }

            $boostRecipient = $this->factory->create($mailing->getBoostMailing(), $cgc);
            $recipient->setBoostMailingRecipient($boostRecipient);
            $mrs[] = $boostRecipient;
        }

        return $mrs;
    }

    private function doesContactsGroupContactAcceptEmail(ContactsGroupContact $cgc)
    {
        if (!$cgc->acceptEmails()) {
            return false;
        }

        if (!$cgc->getContact()->getEmail()) {
            return false;
        }

        return true;
    }
}
