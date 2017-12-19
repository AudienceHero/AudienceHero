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

namespace AudienceHero\Bundle\MailingCampaignBundle\Tests\Mailer\Model;

use AudienceHero\Bundle\CoreBundle\Bridge\Sylius\Mailer\Model\IdentifiableEmailInterface;
use AudienceHero\Bundle\CoreBundle\Bridge\Sylius\Mailer\Model\TaggableEmailInterface;
use AudienceHero\Bundle\CoreBundle\Bridge\Sylius\Mailer\Model\TrackableEmailInterface;
use AudienceHero\Bundle\MailingCampaignBundle\Entity\Mailing;
use AudienceHero\Bundle\MailingCampaignBundle\Entity\MailingRecipient;
use AudienceHero\Bundle\MailingCampaignBundle\Mailer\Model\MailingCampaignEmail;
use PHPUnit\Framework\TestCase;

class MailingCampaignEmailTest extends TestCase
{
    public function testMailingCampaignEmail()
    {
        $email = new MailingCampaignEmail();
        $this->assertInstanceOf(TaggableEmailInterface::class, $email);
        $this->assertInstanceOf(TrackableEmailInterface::class, $email);
        $this->assertInstanceOf(IdentifiableEmailInterface::class, $email);
        $this->assertSame(['mailing_campaign'], $email->getTags());
        $this->assertTrue($email->trackClicks());
        $this->assertTrue($email->trackOpens());

        $mailing = new Mailing();
        $mailingRecipient = new MailingRecipient();

        $email->setMailing($mailing);
        $email->setMailingRecipient($mailingRecipient);

        $this->assertSame($mailing, $email->getMailing());
        $this->assertSame($mailingRecipient, $email->getMailingRecipient());
    }
}
