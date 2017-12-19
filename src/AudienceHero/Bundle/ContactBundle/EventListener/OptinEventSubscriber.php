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

namespace AudienceHero\Bundle\ContactBundle\EventListener;

use AudienceHero\Bundle\ContactBundle\Event\ContactEvent;
use AudienceHero\Bundle\ContactBundle\Event\ContactEvents;
use AudienceHero\Bundle\ContactBundle\Mailer\Model\OptinConfirmedEmail;
use AudienceHero\Bundle\ContactBundle\Mailer\Model\OptinRequestEmail;
use AudienceHero\Bundle\CoreBundle\Mailer\TransactionalMailer;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * OptinEventSubscriber.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
class OptinEventSubscriber implements EventSubscriberInterface
{
    /**
     * @var TransactionalMailer
     */
    private $transactionalMailer;

    public function __construct(TransactionalMailer $transactionalMailer)
    {
        $this->transactionalMailer = $transactionalMailer;
    }

    public static function getSubscribedEvents()
    {
        return [
            ContactEvents::OPT_IN_REQUEST => 'onOptinRequest',
            ContactEvents::OPT_IN_CONFIRMED => 'onOptinConfirmed',
        ];
    }

    public function onOptinRequest(ContactEvent $contactEvent)
    {
        $contact = $contactEvent->getContact();
        if ($contact->getEmail()) {
            $this->transactionalMailer->send(
                OptinRequestEmail::class,
                $contactEvent->getContact()->getOwner(),
                [
                'contactsGroupContact' => $contactEvent->getContactsGroupContact(),
                'contactsGroup' => $contactEvent->getContactsGroup(),
                'contactsGroupForm' => $contactEvent->getContactsGroupForm(),
                'contact' => $contactEvent->getContact(),
            ],
                $contact->getEmail()
            );
        }
    }

    public function onOptinConfirmed(ContactEvent $contactEvent)
    {
        $contact = $contactEvent->getContact();
        if ($contact->getEmail()) {
            $this->transactionalMailer->send(
                OptinConfirmedEmail::class,
                $contactEvent->getContact()->getOwner(),
                [
                'contactsGroupContact' => $contactEvent->getContactsGroupContact(),
                'contactsGroup' => $contactEvent->getContactsGroup(),
                'contactsGroupForm' => $contactEvent->getContactsGroupForm(),
                'contact' => $contactEvent->getContact(),
            ],
                $contact->getEmail()
            );
        }
    }
}
