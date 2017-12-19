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

use AudienceHero\Bundle\CoreBundle\Serializer\MessageSerializer;
use AudienceHero\Bundle\MailingCampaignBundle\Entity\Mailing;
use AudienceHero\Bundle\MailingCampaignBundle\Entity\MailingRecipient;
use AudienceHero\Bundle\MailingCampaignBundle\Factory\MailingRecipientCollectionFactory;
use AudienceHero\Bundle\MailingCampaignBundle\Queue\MailingMessage;
use AudienceHero\Bundle\MailingCampaignBundle\Queue\MailingProducer;
use AudienceHero\Bundle\MailingCampaignBundle\Queue\Processor\MailingSendProcessor;
use Doctrine\ORM\EntityManagerInterface;
use Enqueue\Consumption\Result;
use Interop\Queue\PsrContext;
use Interop\Queue\PsrMessage;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;

class MailingSendProcessorTest extends TestCase
{
    /** @var ObjectProphecy */
    private $logger;
    /** @var ObjectProphecy */
    private $serializer;
    /** @var ObjectProphecy */
    private $collectionFactory;
    /** @var ObjectProphecy */
    private $registry;
    /** @var ObjectProphecy */
    private $producer;
    /** @var ObjectProphecy */
    private $message;
    /** @var ObjectProphecy */
    private $context;
    /** @var ObjectProphecy */
    private $manager;

    public function setUp()
    {
        $this->producer = $this->prophesize(MailingProducer::class);
        $this->manager = $this->prophesize(EntityManagerInterface::class);
        $this->registry = $this->prophesize(RegistryInterface::class);
        $this->collectionFactory = $this->prophesize(MailingRecipientCollectionFactory::class);
        $this->serializer = $this->prophesize(MessageSerializer::class);
        $this->logger = $this->prophesize(LoggerInterface::class);

        $this->context = $this->prophesize(PsrContext::class);
        $this->message = $this->prophesize(PsrMessage::class);
    }

    public function testGetSubscribedCommand()
    {
        $this->assertSame(
            [
            'processorName' => MailingProducer::MAILING_SEND,
            'queueName' => MailingProducer::MAILING_SEND,
            'queueNameHardcoded' => true,
            'exclusive' => true,
        ],
            MailingSendProcessor::getSubscribedCommand()
        );
    }

    private function getProcessorInstance(): MailingSendProcessor
    {
        $this->message->getBody()->shouldBeCalled()->willReturn('{}');

        return new MailingSendProcessor(
            $this->producer->reveal(),
            $this->registry->reveal(),
            $this->collectionFactory->reveal(),
            $this->serializer->reveal(),
            $this->logger->reveal()
        );
    }

    public function testReturnsRejectIfMessageIsNotFound()
    {
        $processor = $this->getProcessorInstance();
        $this->serializer->deserialize('{}', MailingMessage::class)
                         ->shouldBeCalled()
                         ->willReturn(null);

        $result = $processor->process($this->message->reveal(), $this->context->reveal());
        $this->assertInstanceOf(Result::class, $result);
        $this->assertSame(Result::REJECT, $result->getStatus());
        $this->assertSame('No Message', $result->getReason());
    }

    public function testReturnsRejectIfNoMailingInMessage()
    {
        $processor = $this->getProcessorInstance();
        $message = new MailingMessage();
        $this->serializer->deserialize('{}', MailingMessage::class)
            ->shouldBeCalled()
            ->willReturn($message);

        $result = $processor->process($this->message->reveal(), $this->context->reveal());
        $this->assertInstanceOf(Result::class, $result);
        $this->assertSame(Result::REJECT, $result->getStatus());
        $this->assertSame('No Mailing found in message', $result->getReason());
    }

    public function testReturnsRejectIfMailingIsNotInPendingStatus()
    {
        $processor = $this->getProcessorInstance();
        $mailing = new Mailing();
        $message = new MailingMessage();
        $message->setMailing($mailing);
        $this->serializer->deserialize('{}', MailingMessage::class)
            ->shouldBeCalled()
            ->willReturn($message);

        $result = $processor->process($this->message->reveal(), $this->context->reveal());
        $this->assertInstanceOf(Result::class, $result);
        $this->assertSame(Result::REJECT, $result->getStatus());
        $this->assertSame('Mailing is in status different from PENDING, skipping send. (status=draft)', $result->getReason());
    }

    public function testProcess()
    {
        $processor = $this->getProcessorInstance();
        $mailing = new Mailing();
        $mailing->setStatus(Mailing::STATUS_PENDING);
        $message = new MailingMessage();
        $message->setMailing($mailing);
        $this->serializer->deserialize('{}', MailingMessage::class)
            ->shouldBeCalled()
            ->willReturn($message);

        $mrs = [
            new MailingRecipient(),
            new MailingRecipient(),
        ];

        $this->manager->persist($mrs[0])->shouldBeCalled();
        $this->manager->persist($mrs[1])->shouldBeCalled();
        $this->manager->flush()->shouldBeCalled();

        $this->producer->sendMailingRecipient($mrs[0])->shouldBeCalled();
        $this->producer->sendMailingRecipient($mrs[1])->shouldBeCalled();

        $this->registry->getEntityManager()
                       ->shouldBeCalled()
                       ->willReturn($this->manager->reveal());

        $this->collectionFactory->createCollection($mailing)
                                ->shouldBeCalled()
                                ->willReturn($mrs);

        $result = $processor->process($this->message->reveal(), $this->context->reveal());
        $this->assertInstanceOf(Result::class, $result);
        $this->assertSame(Result::ACK, $result->getStatus());
        $this->assertTrue($mailing->isStatusDelivering());
    }
}
