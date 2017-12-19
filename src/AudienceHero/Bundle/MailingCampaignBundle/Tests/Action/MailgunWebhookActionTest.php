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

namespace AudienceHero\Bundle\MailingCampaignBundle\Tests\Action;

use AudienceHero\Bundle\CoreBundle\Bridge\Sylius\Mailer\Model\IdentifiableEmailInterface;
use AudienceHero\Bundle\CoreBundle\Entity\User;
use AudienceHero\Bundle\MailingCampaignBundle\Action\MailgunWebhookAction;
use AudienceHero\Bundle\MailingCampaignBundle\Entity\Email;
use AudienceHero\Bundle\MailingCampaignBundle\Entity\EmailEvent;
use AudienceHero\Bundle\MailingCampaignBundle\Factory\EmailEventFactory;
use AudienceHero\Bundle\MailingCampaignBundle\Repository\EmailRepository;
use AudienceHero\Bundle\MailingCampaignBundle\Webhook\WebhookSignatureVerifierInterface;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class MailgunWebhookActionTest extends TestCase
{
    /** @var ObjectProphecy */
    private $repository;
    /** @var ObjectProphecy */
    private $registry;
    /** @var ObjectProphecy */
    private $manager;
    /** @var ObjectProphecy */
    private $verifier;
    /** @var ObjectProphecy */
    private $emailEventFactory;
    /** @var ObjectProphecy */
    private $logger;

    public function setUp()
    {
        $this->repository = $this->prophesize(EmailRepository::class);
        $this->registry = $this->prophesize(RegistryInterface::class);
        $this->manager = $this->prophesize(EntityManagerInterface::class);
        $this->verifier = $this->prophesize(WebhookSignatureVerifierInterface::class);
        $this->emailEventFactory = $this->prophesize(EmailEventFactory::class);
        $this->logger = $this->prophesize(LoggerInterface::class);
    }

    public function getActionInstance(): MailgunWebhookAction
    {
        return new MailgunWebhookAction(
            $this->registry->reveal(),
            $this->emailEventFactory->reveal(),
            $this->verifier->reveal(),
            $this->repository->reveal(),
            $this->logger->reveal()
        );
    }

    public function getRequestInstance(array $parameters): Request
    {
        $request = new Request();
        $request->request->replace($parameters);

        return $request;
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\BadRequestHttpException
     */
    public function testExceptionIsThrownIfSignatureIsNotVerified()
    {
        $data = ['foo' => 'bar'];
        $this->verifier->isValid($data)->shouldBeCalled()->willReturn(false);
        $action = $this->getActionInstance();
        $action($this->getRequestInstance($data));
    }

    public function test200IfPayloadDoesNotHaveIdentifier()
    {
        $data = ['foo' => 'bar'];
        $this->repository->findOneOrNullByIdentifier()->shouldNotBeCalled();
        $this->verifier->isValid($data)->shouldBeCalled()->willReturn(true);
        $action = $this->getActionInstance();
        $response = $action($this->getRequestInstance($data));
        $this->assertInstanceOf(Response::class, $response);
        $this->assertSame('', $response->getContent());
        $this->assertSame(200, $response->getStatusCode());
    }

    public function test200IfNoEmailIsFound()
    {
        $data = [IdentifiableEmailInterface::ATTRIBUTE => '{"id": "my_id"}'];
        $this->repository->findOneOrNullByIdentifier('my_id')->shouldBeCalled()->willReturn(null);
        $this->verifier->isValid($data)->shouldBeCalled()->willReturn(true);
        $action = $this->getActionInstance();
        $response = $action($this->getRequestInstance($data));
        $this->assertSame('', $response->getContent());
        $this->assertSame(200, $response->getStatusCode());
    }

    public function test200IfNoEmailEventIsCreated()
    {
        $data = [IdentifiableEmailInterface::ATTRIBUTE => '{"id": "my_id"}'];

        $email = new Email();
        $email->setOwner(new User());

        $this->repository->findOneOrNullByIdentifier('my_id')
                         ->shouldBeCalled()
                         ->willReturn($email);

        $this->emailEventFactory->createFromMailgunWebhook($data)
              ->shouldBeCalled()
              ->willReturn(null);

        $this->registry->getManager()->shouldNotBeCalled();

        $this->verifier->isValid($data)->shouldBeCalled()->willReturn(true);
        $action = $this->getActionInstance();
        $response = $action($this->getRequestInstance($data));
        $this->assertSame('', $response->getContent());
        $this->assertSame(200, $response->getStatusCode());
    }

    public function testAction()
    {
        $data = [IdentifiableEmailInterface::ATTRIBUTE => '{"id": "my_id"}'];

        $email = new Email();
        $email->setOwner(new User());

        $this->repository->findOneOrNullByIdentifier('my_id')
            ->shouldBeCalled()
            ->willReturn($email);

        $emailEvent = new EmailEvent();
        $this->emailEventFactory->createFromMailgunWebhook($data)
            ->shouldBeCalled()
            ->willReturn($emailEvent);

        $this->manager->persist($emailEvent)->shouldBeCalled();
        $this->manager->flush()->shouldBeCalled();
        $this->registry->getManager()->willReturn($this->manager->reveal())->shouldBeCalled();

        $this->verifier->isValid($data)->shouldBeCalled()->willReturn(true);
        $action = $this->getActionInstance();
        $response = $action($this->getRequestInstance($data));
        $this->assertSame('', $response->getContent());
        $this->assertSame(200, $response->getStatusCode());

        $this->assertContains($emailEvent, $email->getEvents());
    }
}
