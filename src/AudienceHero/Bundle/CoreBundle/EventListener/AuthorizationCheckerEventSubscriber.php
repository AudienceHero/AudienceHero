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

namespace AudienceHero\Bundle\CoreBundle\EventListener;

use ApiPlatform\Core\DataProvider\PaginatorInterface;
use ApiPlatform\Core\EventListener\EventPriorities;
use AudienceHero\Bundle\CoreBundle\Behavior\Ownable\OwnableInterface;
use AudienceHero\Bundle\CoreBundle\Behavior\Publishable\PublishableInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * AuthorizationCheckerEventSubscriber.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
class AuthorizationCheckerEventSubscriber implements EventSubscriberInterface
{
    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    public function __construct(AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->authorizationChecker = $authorizationChecker;
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => ['onPostRead', EventPriorities::POST_READ],
        ];
    }

    public function onPostRead(GetResponseEvent $event): void
    {
        $data = $event->getRequest()->attributes->get('data');
        if (!$data) {
            return;
        }

        if (!is_object($data)) {
            return;
        }

        if ($data instanceof PaginatorInterface) {
            return;
        }

        // If it is possible to see the object, do nothing.
        if ($data instanceof PublishableInterface && $this->authorizationChecker->isGranted('FRONT_SEE', $data)) {
            return;
        }

        if (!$data instanceof OwnableInterface) {
            return;
        }

        // If the currently logged person is the owner, move along as well
        if ($this->authorizationChecker->isGranted('IS_OWNER', $data)) {
            return;
        }

        // At this point, this person should not be able to see the object
        throw new AccessDeniedHttpException('You are not allowed to access this resource.');
    }
}
