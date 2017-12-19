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

namespace AudienceHero\Bundle\MailingCampaignBundle\Tests\Mailer;

use AudienceHero\Bundle\CoreBundle\Entity\User;
use AudienceHero\Bundle\MailingCampaignBundle\Entity\Mailing;
use AudienceHero\Bundle\MailingCampaignBundle\Entity\MailingRecipient;
use AudienceHero\Bundle\MailingCampaignBundle\Factory\EmailFactory;
use AudienceHero\Bundle\MailingCampaignBundle\Factory\MailingCampaignEmailFactory;
use AudienceHero\Bundle\MailingCampaignBundle\Mailer\Mailer;
use AudienceHero\Bundle\MailingCampaignBundle\Mailer\Model\MailingCampaignEmail;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;
use Sylius\Component\Mailer\Renderer\Adapter\AdapterInterface as RendererAdapterInterface;
use Sylius\Component\Mailer\Renderer\RenderedEmail;
use Sylius\Component\Mailer\Sender\Adapter\AdapterInterface as SenderAdapterInterface;

class MailerTest extends TestCase
{
    /** @var ObjectProphecy */
    private $emailFactory;
    /** @var ObjectProphecy */
    private $renderer;
    /** @var ObjectProphecy */
    private $sender;
    /** @var ObjectProphecy */
    private $mailingCampaignEmailFactory;
    private $owner;

    public function setUp()
    {
        $this->owner = new User();
        $this->renderer = $this->prophesize(RendererAdapterInterface::class);
        $this->sender = $this->prophesize(SenderAdapterInterface::class);
        $this->mailingCampaignEmailFactory = $this->prophesize(MailingCampaignEmailFactory::class);
        $this->emailFactory = $this->prophesize(EmailFactory::class);
    }

    public function testSend()
    {
        $mailing = new Mailing();
        $mailing->setOwner($this->owner);
        $mr = new MailingRecipient();
        $mr->setToEmail('foobar@example.com');
        $mr->setToName('Foo Bar');
        $mr->setMailing($mailing);

        $mailingCampaignEmail = new MailingCampaignEmail();
        $mailingCampaignEmail->setSenderAddress('sender@example.com');
        $mailingCampaignEmail->setSenderName('Jean Sender');

        $this->mailingCampaignEmailFactory
             ->createClassic($mailing, $mr)
             ->shouldBeCalled()
             ->willReturn($mailingCampaignEmail);

        $renderedEmail = new RenderedEmail('foo', 'bar');

        $data = ['mailing_recipient' => $mr, 'mailing' => $mailing];
        $this->renderer->render($mailingCampaignEmail, $data)
                       ->shouldBeCalled()
                       ->willReturn($renderedEmail);

        $this->sender->send(
            ['foobar@example.com' => 'Foo Bar'],
            'sender@example.com',
            'Jean Sender',
            $renderedEmail,
            $mailingCampaignEmail,
            $data,
            []
        )->shouldBeCalled();

        $mailer = new Mailer($this->renderer->reveal(), $this->sender->reveal(), $this->mailingCampaignEmailFactory->reveal(), $this->emailFactory->reveal());
        $mailer->send($mr);
    }

    public function testSendPreview()
    {
        $mailing = new Mailing();
        $mailing->setOwner($this->owner);

        $mailingCampaignEmail = new MailingCampaignEmail();
        $mailingCampaignEmail->setSenderAddress('sender@example.com');
        $mailingCampaignEmail->setSenderName('Jean Sender');

        $this->mailingCampaignEmailFactory
            ->createClassic($mailing, Argument::type(MailingRecipient::class))
            ->shouldBeCalled()
            ->willReturn($mailingCampaignEmail);

        $renderedEmail = new RenderedEmail('foo', 'bar');

        $this->renderer->render($mailingCampaignEmail, Argument::type('array'))
            ->shouldBeCalled()
            ->willReturn($renderedEmail);

        $this->sender->send(
            ['test@example.com' => 'AudienceHero Test Recipient'],
            'sender@example.com',
            'Jean Sender',
            $renderedEmail,
            $mailingCampaignEmail,
            Argument::type('array'),
            []
        )->shouldBeCalled();

        $mailer = new Mailer($this->renderer->reveal(), $this->sender->reveal(), $this->mailingCampaignEmailFactory->reveal(), $this->emailFactory->reveal());
        $mailer->sendPreview($mailing, 'test@example.com');
    }
}
