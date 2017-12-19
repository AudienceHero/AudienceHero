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

use AudienceHero\Bundle\CoreBundle\Generator\UUIDGenerator;
use AudienceHero\Bundle\MailingCampaignBundle\Entity\Mailing;
use AudienceHero\Bundle\MailingCampaignBundle\Entity\MailingRecipient;
use AudienceHero\Bundle\MailingCampaignBundle\Mailer\Model\MailingCampaignEmail;

/**
 * EmailFactory.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
class MailingCampaignEmailFactory
{
    /**
     * @var UUIDGenerator
     */
    private $generator;

    public function __construct(UUIDGenerator $generator)
    {
        $this->generator = $generator;
    }

    public function createClassic(Mailing $mailing, MailingRecipient $mailingRecipient)
    {
        $email = new MailingCampaignEmail();
        $email->setSenderName($mailing->getFromName());
        $email->setSenderAddress($mailing->getPersonEmail()->getEmail());
        $email->setTemplate('AudienceHeroMailingCampaignBundle:mailer:classic.html.twig');
        $email->setEnabled(true);
        $email->setMailing($mailing);
        $email->setMailingRecipient($mailingRecipient);
        $email->setIdentifier($this->generator->generate());

        return $email;
    }
}
