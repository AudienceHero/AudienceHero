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

use AudienceHero\Bundle\CoreBundle\Bridge\Sylius\Mailer\Model\IdentifiableEmailInterface;
use AudienceHero\Bundle\CoreBundle\Bridge\Sylius\Mailer\Model\TaggableEmailInterface;
use AudienceHero\Bundle\CoreBundle\Bridge\Sylius\Mailer\Model\TrackableEmailInterface;
use AudienceHero\Bundle\CoreBundle\Generator\UUIDGenerator;
use AudienceHero\Bundle\CoreBundle\Mailer\Util\CssInliner;
use Sylius\Component\Mailer\Event\EmailSendEvent;
use Sylius\Component\Mailer\SyliusMailerEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * EmailHeadersEventSubscriber.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
class MailgunEmailHeadersEventSubscriber implements EventSubscriberInterface
{
    /**
     * @var CssInliner
     */
    private $cssInliner;
    /**
     * @var UUIDGenerator
     */
    private $generator;

    public function __construct(CssInliner $cssInliner, UUIDGenerator $generator)
    {
        $this->cssInliner = $cssInliner;
        $this->generator = $generator;
    }

    public static function getSubscribedEvents()
    {
        return [
            SyliusMailerEvents::EMAIL_PRE_SEND => 'onPreSend',
        ];
    }

    public function onPreSend(EmailSendEvent $event)
    {
        $m = $event->getMessage();
        if (!$m instanceof \Swift_Message) {
            return;
        }

        if ('text/html' === $m->getContentType()) {
            $m->setBody($this->cssInliner->inline($m->getBody()), 'text/html');
        }

        $email = $event->getEmail();
        if ($email instanceof TrackableEmailInterface) {
            $m->getHeaders()->addTextHeader('X-Mailgun-Track-Opens', $email->trackOpens() ? 'yes' : 'no');
            $m->getHeaders()->addTextHeader('X-Mailgun-Track-Clicks', $email->trackClicks() ? 'yes' : 'no');
        }

        if ($email instanceof TaggableEmailInterface) {
            foreach ($email->getTags() as $tag) {
                $m->getHeaders()->addTextHeader('X-Mailgun-Tag', $tag);
            }
        }

        if ($email instanceof IdentifiableEmailInterface) {
            $m->getHeaders()->addTextHeader('X-Mailgun-Variables', json_encode([IdentifiableEmailInterface::ATTRIBUTE => $email->getIdentifier()]));
        }
    }
}
