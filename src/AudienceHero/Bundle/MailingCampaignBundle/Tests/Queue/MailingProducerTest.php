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

use AudienceHero\Bundle\CoreBundle\Entity\User;
use AudienceHero\Bundle\CoreBundle\Queue\Producer;
use AudienceHero\Bundle\MailingCampaignBundle\Entity\Mailing;
use AudienceHero\Bundle\MailingCampaignBundle\Entity\MailingRecipient;
use AudienceHero\Bundle\MailingCampaignBundle\Queue\MailingMessage;
use AudienceHero\Bundle\MailingCampaignBundle\Queue\MailingProducer;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;

class MailingProducerTest extends TestCase
{
    /** @var ObjectProphecy */
    private $producer;

    public function setUp()
    {
        $this->producer = $this->prophesize(Producer::class);
    }

    public function testSendMailing()
    {
        $mailing = new Mailing();
        $mailing->setOwner(new User());

        $this->producer->sendCommand(
            MailingProducer::MAILING_SEND,
            Argument::that(function (MailingMessage $message) use ($mailing) {
                return null === $message->getMailingRecipient() &&
                    $mailing === $message->getMailing();
            })
        )->shouldBeCalled();

        $producer = new MailingProducer($this->producer->reveal());
        $producer->sendMailing($mailing);
    }

    public function testSendMailingRecipient()
    {
        $mailing = new Mailing();
        $mailing->setOwner(new User());
        $mailingRecipient = new MailingRecipient();
        $mailingRecipient->setMailing($mailing);

        $this->producer->sendCommand(
            MailingProducer::MAILING_RECIPIENT_SEND,
            Argument::that(function (MailingMessage $message) use ($mailing, $mailingRecipient) {
                return $mailingRecipient === $message->getMailingRecipient() &&
                    $mailing === $message->getMailing();
            })
        )->shouldBeCalled();

        $producer = new MailingProducer($this->producer->reveal());
        $producer->sendMailingRecipient($mailingRecipient);
    }
}
