<?php

/*
 * This file is part of the AudienceHero project.
 *
 * (c) Marc Weistroff <marc@weistroff.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AudienceHero\Bundle\MailingCampaignBundle\Tests\Factory;

use AudienceHero\Bundle\MailingCampaignBundle\Entity\Mailing;
use AudienceHero\Bundle\MailingCampaignBundle\Entity\MailingRecipient;
use AudienceHero\Bundle\MailingCampaignBundle\Factory\MailingFactory;
use PHPUnit\Framework\TestCase;

class MailingFactoryTest extends TestCase
{
    public function testCreateBoost()
    {
        $factory = new MailingFactory();

        $mailing = new Mailing();
        $mailing->setReference('Reference');
        $mailing->setStatus(Mailing::STATUS_DELIVERING);
        $mailing->setIsInternal(false);
        $mailing->setRecipients([new MailingRecipient()]);
        $mailing->setSubject('Subject');

        $factory->createBoost($mailing);
        $this->assertNotNull($mailing->getBoostMailing());
        $boost = $mailing->getBoostMailing();
        $this->assertTrue($boost->getIsInternal());
        $this->assertEmpty($boost->getRecipients());
        $this->assertSame(Mailing::STATUS_DRAFT, $boost->getStatus());
        $this->assertSame('Boost Reference', $boost->getReference());
        $this->assertSame('Re: Subject', $boost->getSubject());
    }
}
