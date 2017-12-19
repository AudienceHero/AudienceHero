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

namespace AudienceHero\Bundle\MailingCampaignBundle\Action;

use AudienceHero\Bundle\CoreBundle\Bridge\Sylius\Mailer\Model\IdentifiableEmailInterface;
use AudienceHero\Bundle\MailingCampaignBundle\Entity\Email;
use AudienceHero\Bundle\MailingCampaignBundle\Factory\EmailEventFactory;
use AudienceHero\Bundle\MailingCampaignBundle\Repository\EmailRepository;
use AudienceHero\Bundle\MailingCampaignBundle\Webhook\WebhookSignatureVerifierInterface;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Mailgun\Mailgun;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * MailgunWebhookAction.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
class MailgunWebhookAction
{
    /**
     * @var EmailRepository
     */
    private $repository;
    /**
     * @var Registry
     */
    private $registry;
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var WebhookSignatureVerifierInterface
     */
    private $verifier;
    /**
     * @var EmailEventFactory
     */
    private $emailEventFactory;

    public function __construct(RegistryInterface $registry, EmailEventFactory $emailEventFactory, WebhookSignatureVerifierInterface $verifier, EmailRepository $repository, LoggerInterface $logger)
    {
        $this->repository = $repository;
        $this->registry = $registry;
        $this->logger = $logger;
        $this->verifier = $verifier;
        $this->emailEventFactory = $emailEventFactory;
    }

    /**
     * @Route("/webhook/mailgun")
     * @Method({"POST"})
     */
    public function __invoke(Request $request)
    {
        if (!$this->verifier->isValid($request->request->all())) {
            throw new BadRequestHttpException('Mailgun webhook signature could not be verified');
        }

        $json = $request->request->get(IdentifiableEmailInterface::ATTRIBUTE);
        if (!$json) {
            return new Response('', 200);
        }

        $id = @json_decode($json, true);
        if (null === $id) {
            $this->logger->info('Payload does not have an identifier. Skipping.', ['payload' => $request->request->all()]);

            return new Response('', 200);
        }

        /** @var Email $mail */
        $mail = $this->repository->findOneOrNullByIdentifier($id['id']);
        if (!$mail) {
            return new Response('', 200);
        }

        $emailEvent = $this->emailEventFactory->createFromMailgunWebhook($request->request->all());
        if (!$emailEvent) {
            return new Response('', 200);
        }

        $mail->addEvent($emailEvent);

        $em = $this->registry->getManager();
        $em->persist($emailEvent);
        $em->flush();

        return new Response('', 200);
    }
}
