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

namespace AudienceHero\Bundle\CoreBundle\Bridge\Sylius\Mailer\Sender\Adapter;

use Mailgun\Mailgun;
use Sylius\Component\Mailer\Event\EmailSendEvent;
use Sylius\Component\Mailer\Model\EmailInterface;
use Sylius\Component\Mailer\Renderer\RenderedEmail;
use Sylius\Component\Mailer\Sender\Adapter\AdapterInterface;
use Sylius\Component\Mailer\SyliusMailerEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * MailgunAdapter.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
class MailgunAdapter implements AdapterInterface
{
    /**
     * @var Mailgun
     */
    private $mailgun;

    /**
     * @var string
     */
    private $domain;

    /**
     * @var MailgunMessageBuilder
     */
    private $messageBuilder;
    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /** @var bool */
    private $isTestDelivery;

    public function __construct(MailgunMessageBuilder $messageBuilder, Mailgun $mailgun, string $domain, EventDispatcherInterface $eventDispatcher, bool $isTestDelivery)
    {
        $this->mailgun = $mailgun;
        $this->domain = $domain;
        $this->messageBuilder = $messageBuilder;
        $this->isTestDelivery = $isTestDelivery;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param array          $recipients
     * @param string         $senderAddress
     * @param string         $senderName
     * @param RenderedEmail  $renderedEmail
     * @param EmailInterface $email
     * @param array          $data
     * @param array          $attachments
     */
    public function send(array $recipients, $senderAddress, $senderName, RenderedEmail $renderedEmail, EmailInterface $email, array $data, array $attachments = [])
    {
        $message = $this->messageBuilder->build($recipients, $senderAddress, $senderName, $renderedEmail, $email, $data, $attachments);
        $emailSendEvent = new EmailSendEvent($message, $email, $data, $recipients);
        if ($this->isTestDelivery) {
            $message->getHeaders()->addTextHeader('X-Mailgun-Drop-Message', 'yes');
        }

        $this->eventDispatcher->dispatch(SyliusMailerEvents::EMAIL_PRE_SEND, $emailSendEvent);
        $result = $this->mailgun->messages()->sendMime($this->domain, $message->getTo(), $message->toString(), []);

        if (200 !== $result->getStatusCode()) {
            throw new \RuntimeException(sprintf('Mailer transport responsed with an error. %d. %s', $result->http_response_code, var_export($result, true)));
        }

        $this->eventDispatcher->dispatch(SyliusMailerEvents::EMAIL_POST_SEND, $emailSendEvent);
    }
}
