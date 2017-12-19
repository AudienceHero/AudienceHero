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

namespace AudienceHero\Bundle\MailingCampaignBundle\EventListener;

use AudienceHero\Bundle\MailingCampaignBundle\Entity\Email;
use AudienceHero\Bundle\MailingCampaignBundle\Entity\MailingRecipient;
use AudienceHero\Bundle\MailingCampaignBundle\Factory\EmailFactory;
use AudienceHero\Bundle\MailingCampaignBundle\Mailer\Model\MailingCampaignEmail;
use AudienceHero\Bundle\MailingCampaignBundle\Repository\EmailRepository;
use Psr\Log\LoggerInterface;
use Sylius\Component\Mailer\Event\EmailSendEvent;
use Sylius\Component\Mailer\SyliusMailerEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * MailingRecipientEmailEventSubscriber.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
class MailingRecipientEmailEventSubscriber implements EventSubscriberInterface
{
    /**
     * @var EmailFactory
     */
    private $emailFactory;
    /**
     * @var EmailRepository
     */
    private $repository;
    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(EmailFactory $emailFactory, EmailRepository $repository, LoggerInterface $logger)
    {
        $this->emailFactory = $emailFactory;
        $this->repository = $repository;
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            SyliusMailerEvents::EMAIL_POST_SEND => 'postSend',
        ];
    }

    public function postSend(EmailSendEvent $event): void
    {
        /** @var MailingCampaignEmail $email */
        $campaignEmail = $event->getEmail();
        if (!$campaignEmail instanceof MailingCampaignEmail) {
            return;
        }

        /** @var MailingRecipient $mr */
        $mr = $campaignEmail->getMailingRecipient();
        if ($mr->getIsTest()) {
            return;
        }

        /** @var Email $email */
        $email = $this->emailFactory->create($mr);
        $email->setMandrillId($campaignEmail->getIdentifier());
        $mr->setStatus(MailingRecipient::STATUS_SENT);

        $this->repository->persistAndFlush($email);
    }
}
