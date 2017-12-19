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

namespace AudienceHero\Bundle\MailingCampaignBundle\Tests\EventListener;

use AudienceHero\Bundle\MailingCampaignBundle\Entity\Email as EmailEntity;
use AudienceHero\Bundle\MailingCampaignBundle\Entity\MailingRecipient;
use AudienceHero\Bundle\MailingCampaignBundle\EventListener\MailingRecipientEmailEventSubscriber;
use AudienceHero\Bundle\MailingCampaignBundle\Factory\EmailFactory;
use AudienceHero\Bundle\MailingCampaignBundle\Mailer\Model\MailingCampaignEmail;
use AudienceHero\Bundle\MailingCampaignBundle\Repository\EmailRepository;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;
use Psr\Log\LoggerInterface;
use Sylius\Component\Mailer\Event\EmailSendEvent;
use Sylius\Component\Mailer\Model\Email;
use Sylius\Component\Mailer\SyliusMailerEvents;

class MailingRecipientEmailEventSubscriberTest extends TestCase
{
    /** @var ObjectProphecy */
    private $logger;
    /** @var ObjectProphecy */
    private $emailFactory;
    /** @var ObjectProphecy */
    private $emailRepository;

    public function setUp()
    {
        $this->logger = $this->prophesize(LoggerInterface::class);
        $this->emailFactory = $this->prophesize(EmailFactory::class);
        $this->emailRepository = $this->prophesize(EmailRepository::class);
    }

    public function testGetSubscribedEvent()
    {
        $this->assertSame([SyliusMailerEvents::EMAIL_POST_SEND => 'postSend'], MailingRecipientEmailEventSubscriber::getSubscribedEvents());
    }

    public function testSubscriberDoesNotTriggerWhenEmailIsNotRightType()
    {
        $this->emailFactory->create(Argument::any())->shouldNotBeCalled();
        $this->emailRepository->persistAndFlush(Argument::any())->shouldNotBeCalled();

        $email = new Email();
        $event = new EmailSendEvent(null, $email, []);

        $subscriber = new MailingRecipientEmailEventSubscriber($this->emailFactory->reveal(), $this->emailRepository->reveal(), $this->logger->reveal());
        $subscriber->postSend($event);
    }

    public function testSubscriberHandlesMailingCampaignEmail()
    {
        $mr = new MailingRecipient();
        $email = new EmailEntity();

        $syliusEmail = new MailingCampaignEmail();
        $syliusEmail->setMailingRecipient($mr);
        $syliusEmail->setIdentifier('foobar');
        $this->emailFactory->create($mr)->shouldBeCalled()->willReturn($email);
        $this->emailRepository->persistAndFlush($email)->shouldBeCalled();

        $event = new EmailSendEvent(null, $syliusEmail, []);
        $subscriber = new MailingRecipientEmailEventSubscriber($this->emailFactory->reveal(), $this->emailRepository->reveal(), $this->logger->reveal());
        $subscriber->postSend($event);

        $this->assertSame('foobar', $email->getMandrillId());
        $this->assertSame(MailingRecipient::STATUS_SENT, $mr->getStatus());
    }
}
