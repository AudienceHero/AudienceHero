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

namespace AudienceHero\Bundle\CoreBundle\Bridge\Sylius\Mailer\EventListener;

use AudienceHero\Bundle\CoreBundle\Bridge\Sylius\Mailer\Model\Mailgun\Message;
use Sylius\Component\Mailer\Event\EmailSendEvent;
use Sylius\Component\Mailer\SyliusMailerEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class MailerDataCollectorEventSubscriber implements EventSubscriberInterface
{
    private $data = [];

    public static function getSubscribedEvents()
    {
        return [SyliusMailerEvents::EMAIL_PRE_SEND => 'onPreSend'];
    }

    public function onPreSend(EmailSendEvent $event)
    {
        $msg = $event->getMessage();
        if (!$msg instanceof Message) {
            return;
        }

        $this->data[] = [
            'tags' => $msg->getTags(),
            'from' => $msg->getFrom(),
            'to' => $msg->getTo(),
            'subject' => $msg->getSubject(),
            'message' => $msg,
        ];
    }

    public function getMessages()
    {
        return $this->data;
    }

    public function getName()
    {
        return 'audiencehero_mailer';
    }
}
