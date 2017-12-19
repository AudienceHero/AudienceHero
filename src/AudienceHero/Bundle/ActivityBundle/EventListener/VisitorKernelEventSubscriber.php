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

namespace AudienceHero\Bundle\ActivityBundle\EventListener;

use AudienceHero\Bundle\ActivityBundle\Entity\Visitor;
use Ramsey\Uuid\Uuid;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class VisitorKernelEventSubscriber implements EventSubscriberInterface
{
    const COOKIE_NAME = 'ah_vst';

    public static function getSubscribedEvents()
    {
        return [
            // must be registered before the default Locale listener
            KernelEvents::REQUEST => [['onKernelRequest', 17]],
            KernelEvents::RESPONSE => [['onKernelResponse', 17]],
        ];
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        // Be nice and don't track users with DNT header
        if ('1' === $request->headers->get('DNT')) {
            $request->attributes->set('do_not_track', true);

            return;
        }

        $visitorId = $request->cookies->get(self::COOKIE_NAME);
        if ($visitorId && Uuid::isValid($visitorId)) {
            $request->attributes->set('visitor', $visitorId);

            return;
        }

        $request->attributes->set('visitor', Uuid::uuid4()->toString());
    }

    public function onKernelResponse(FilterResponseEvent $event)
    {
        $request = $event->getRequest();
        if ($request->attributes->get('do_not_track')) {
            return;
        }

        $visitorId = $request->cookies->get(self::COOKIE_NAME);
        $visitor = $request->attributes->get('visitor');
        if (!$visitor || $visitor === $visitorId) {
            return;
        }

        if ($request->isMethod('GET')) {
            $visitor = $request->attributes->get('visitor');
            $cookie = new Cookie(self::COOKIE_NAME, $visitor);
            $event->getResponse()->headers->setCookie($cookie);
        }
    }
}
