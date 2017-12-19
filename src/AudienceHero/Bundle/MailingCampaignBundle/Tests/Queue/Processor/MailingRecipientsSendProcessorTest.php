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

namespace AudienceHero\Bundle\MailingCampaignBundle\Tests\Queue\Processor;

use AudienceHero\Bundle\CoreBundle\Entity\User;
use AudienceHero\Bundle\CoreBundle\Serializer\MessageSerializer;
use AudienceHero\Bundle\MailingCampaignBundle\Entity\Mailing;
use AudienceHero\Bundle\MailingCampaignBundle\Entity\MailingRecipient;
use AudienceHero\Bundle\MailingCampaignBundle\Mailer\Mailer;
use AudienceHero\Bundle\MailingCampaignBundle\Queue\MailingMessage;
use AudienceHero\Bundle\MailingCampaignBundle\Queue\MailingProducer;
use AudienceHero\Bundle\MailingCampaignBundle\Queue\Processor\MailingRecipientsSendProcessor;
use Enqueue\Consumption\Result;
use Interop\Queue\PsrContext;
use Interop\Queue\PsrMessage;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;
use Psr\Log\LoggerInterface;

class MailingRecipientsSendProcessorTest extends TestCase
{
    /** @var ObjectProphecy */
    private $logger;
    /** @var ObjectProphecy */
    private $serializer;
    /** @var ObjectProphecy */
    private $mailer;
    /** @var ObjectProphecy */
    private $context;
    /** @var ObjectProphecy */
    private $message;

    public function setUp()
    {
        $this->serializer = $this->prophesize(MessageSerializer::class);
        $this->mailer = $this->prophesize(Mailer::class);
        $this->logger = $this->prophesize(LoggerInterface::class);
        $this->context = $this->prophesize(PsrContext::class);
        $this->message = $this->prophesize(PsrMessage::class);
    }

    private function getProcessorInstance()
    {
        $this->message->getBody()->shouldBeCalled()->willReturn('{}');

        return new MailingRecipientsSendProcessor(
            $this->serializer->reveal(),
            $this->mailer->reveal(),
            $this->logger->reveal()
        );
    }

    public function testGetSubscribedCommand()
    {
        $expected = [
            'processorName' => MailingProducer::MAILING_RECIPIENT_SEND,
            'queueName' => MailingProducer::MAILING_RECIPIENT_SEND,
            'queueNameHardcoded' => true,
            'exclusive' => true,
        ];

        $this->assertSame($expected, MailingRecipientsSendProcessor::getSubscribedCommand());
    }

    public function testProcessRejectsIfNoMessage()
    {
        $processor = $this->getProcessorInstance();
        $this->serializer->deserialize('{}', MailingMessage::class)
            ->shouldBeCalled()
            ->willReturn(null);

        $result = $processor->process($this->message->reveal(), $this->context->reveal());
        $this->assertInstanceOf(Result::class, $result);
        $this->assertSame(Result::REJECT, $result->getStatus());
        $this->assertSame('No message', $result->getReason());
    }

    public function testProcessRejectIfMessageDoesNotHaveAMailing()
    {
        $message = new MailingMessage();

        $processor = $this->getProcessorInstance();
        $this->serializer->deserialize('{}', MailingMessage::class)
            ->shouldBeCalled()
            ->willReturn($message);

        $result = $processor->process($this->message->reveal(), $this->context->reveal());
        $this->assertInstanceOf(Result::class, $result);
        $this->assertSame(Result::REJECT, $result->getStatus());
        $this->assertSame('No Mailing in message', $result->getReason());
    }

    public function testProcessRejectIfMessageDoesNotHaveAMailingRecipient()
    {
        $message = new MailingMessage();
        $mailing = new Mailing();
        $message->setMailing($mailing);

        $processor = $this->getProcessorInstance();
        $this->serializer->deserialize('{}', MailingMessage::class)
            ->shouldBeCalled()
            ->willReturn($message);

        $result = $processor->process($this->message->reveal(), $this->context->reveal());
        $this->assertInstanceOf(Result::class, $result);
        $this->assertSame(Result::REJECT, $result->getStatus());
        $this->assertSame('No MailingRecipient in message.', $result->getReason());
    }

    public function testProcessRejectIfMailingRecipientDoesNotHaveAMailing()
    {
        $message = new MailingMessage();
        $mailing = new Mailing();
        $mailingRecipient = new MailingRecipient();
        $message->setMailing($mailing);
        $message->setMailingRecipient($mailingRecipient);

        $processor = $this->getProcessorInstance();
        $this->serializer->deserialize('{}', MailingMessage::class)
            ->shouldBeCalled()
            ->willReturn($message);

        $result = $processor->process($this->message->reveal(), $this->context->reveal());
        $this->assertInstanceOf(Result::class, $result);
        $this->assertSame(Result::REJECT, $result->getStatus());
        $this->assertSame('No Mailing in MailingRecipient .', $result->getReason());
    }

    public function testProcessRejectIfMailingRecipientIsNotPending()
    {
        $owner = new User();
        $message = new MailingMessage();
        $mailing = new Mailing();
        $mailing->setOwner($owner);
        $mailingRecipient = new MailingRecipient();
        $mailingRecipient->setStatus(MailingRecipient::STATUS_SENT);
        $mailingRecipient->setMailing($mailing);
        $message->setMailing($mailing);
        $message->setMailingRecipient($mailingRecipient);

        $processor = $this->getProcessorInstance();
        $this->serializer->deserialize('{}', MailingMessage::class)
            ->shouldBeCalled()
            ->willReturn($message);

        $result = $processor->process($this->message->reveal(), $this->context->reveal());
        $this->assertInstanceOf(Result::class, $result);
        $this->assertSame(Result::REJECT, $result->getStatus());
        $this->assertSame('MailingRecipient  is not in a pending status', $result->getReason());
    }

    public function testProcess()
    {
        $message = new MailingMessage();
        $mailing = new Mailing();
        $mailing->setOwner(new User());
        $mailingRecipient = new MailingRecipient();
        $mailingRecipient->setMailing($mailing);
        $message->setMailing($mailing);
        $message->setMailingRecipient($mailingRecipient);

        $processor = $this->getProcessorInstance();
        $this->serializer->deserialize('{}', MailingMessage::class)
            ->shouldBeCalled()
            ->willReturn($message);

        $this->mailer->send($mailingRecipient)->shouldBeCalled();

        $result = $processor->process($this->message->reveal(), $this->context->reveal());
        $this->assertInstanceOf(Result::class, $result);
        $this->assertSame(Result::ACK, $result->getStatus());
        $this->assertSame(Mailing::STATUS_DELIVERING, $mailingRecipient->getStatus());
    }
}
