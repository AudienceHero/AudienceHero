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

namespace AudienceHero\Bundle\MailingCampaignBundle\Tests\Queue;

use AudienceHero\Bundle\MailingCampaignBundle\Entity\Mailing;
use AudienceHero\Bundle\MailingCampaignBundle\Entity\MailingRecipient;
use AudienceHero\Bundle\MailingCampaignBundle\Queue\MailingMessage;
use PHPUnit\Framework\TestCase;

class MailingMessageTest extends TestCase
{
    public function testAccessors()
    {
        $mailing = new Mailing();
        $mailingRecipient = new MailingRecipient();

        $mailingMessage = MailingMessage::create()->setMailing($mailing)->setMailingRecipient($mailingRecipient);

        $this->assertSame($mailing, $mailingMessage->getMailing());
        $this->assertSame($mailingRecipient, $mailingMessage->getMailingRecipient());
    }
}
