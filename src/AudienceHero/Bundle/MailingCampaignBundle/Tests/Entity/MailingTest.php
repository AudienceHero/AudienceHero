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

namespace AudienceHero\Bundle\MailingCampaignBundle\Tests\Entity;

use AudienceHero\Bundle\MailingCampaignBundle\Entity\Mailing;
use AudienceHero\Bundle\MailingCampaignBundle\Entity\MailingRecipient;

class MailingTest extends \PHPUnit_Framework_TestCase
{
    private $mailing;

    public function testCounters()
    {
        $this->mailing = new Mailing();
        for ($i = 0; $i < 100; ++$i) {
            $mr = new MailingRecipient();
            $this->mailing->getRecipients()->add($mr);
            $mr->setStatus(\AudienceHero\Bundle\MailingCampaignBundle\Entity\MailingRecipient::STATUS_SENT);
        }

        for ($i = 0; $i < 20; ++$i) {
            $this->mailing->getRecipients()->get($i)->setStatus(MailingRecipient::STATUS_OPENED);
            $this->mailing->getRecipients()->get($i)->incrementMailOpenCounter();
            if ($i < 10) {
                $this->mailing->getRecipients()->get($i)->incrementMailOpenCounter();
            }
        }

        for ($i = 0; $i < 5; ++$i) {
            $this->mailing->getRecipients()->get($i)->setStatus(\AudienceHero\Bundle\MailingCampaignBundle\Entity\MailingRecipient::STATUS_OPENED);
            $this->mailing->getRecipients()->get($i)->incrementMailClickCounter();
            if ($i < 3) {
                $this->mailing->getRecipients()->get($i)->incrementMailClickCounter();
            }
        }

        $this->mailing->getRecipients()->last()->setStatus(\AudienceHero\Bundle\MailingCampaignBundle\Entity\MailingRecipient::STATUS_NOT_DELIVERED);
        $this->assertEquals(20, $this->mailing->getCountUniqueOpen());
        $this->assertEquals(5, $this->mailing->getCountUniqueClick());
        $this->assertEquals(1, $this->mailing->getCountNonDelivered());
        $this->assertEquals(99, $this->mailing->getCountDelivered());
        $this->assertEquals(20, $this->mailing->getRateOpen());
        $this->assertEquals(5, $this->mailing->getRateClick());
        $this->assertEquals(99, $this->mailing->getRateDelivery());
        $this->assertEquals(25, $this->mailing->getRateClickByUniqueOpen());
        $this->assertEquals(30, $this->mailing->getCountTotalOpens());
        $this->assertEquals(8, $this->mailing->getCountTotalClicks());
    }
}
