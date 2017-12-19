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

namespace AudienceHero\Bundle\CoreBundle\Behavior\Ownable\EventListener;

use ApiPlatform\Core\EventListener\EventPriorities;
use AudienceHero\Bundle\CoreBundle\Behavior\Ownable\OwnableInterface;
use AudienceHero\Bundle\CoreBundle\Entity\Person;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * OwnableSubscriber.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
class OwnableEventSubscriber implements EventSubscriberInterface
{
    /** @var TokenStorageInterface */
    private $tokenStorage;
    /**
     * @var RegistryInterface
     */
    private $registry;

    public function __construct(TokenStorageInterface $tokenStorage, RegistryInterface $registry)
    {
        $this->tokenStorage = $tokenStorage;
        $this->registry = $registry;
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => ['onDeserializeOwnable', EventPriorities::POST_DESERIALIZE],
        ];
    }

    /**
     * onDeserializeOwnable makes sure that if the deserialized object implements OwnableInterface,
     * the current user is sets as the object's owner.
     *
     * @param GetResponseEvent $event
     */
    public function onDeserializeOwnable(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        if ($request->isMethodSafe() || $request->isMethod(Request::METHOD_DELETE)) {
            return;
        }

        $data = $event->getRequest()->attributes->get('data');

        if ($data instanceof OwnableInterface) {
            $token = $this->tokenStorage->getToken();
            if (!$token) {
                return;
            }

            $user = $token->getUser();
            if (!$user || !$user instanceof Person) {
                return;
            }

            $data->setOwner($user);
            $propertyAccessor = PropertyAccess::createPropertyAccessor();
            $this->setOwnerInNewAssociations($user, $data, $propertyAccessor);
        }
    }

    private function setOwnerInNewAssociations(Person $owner, OwnableInterface $data, PropertyAccessor $propertyAccessor)
    {
        $em = $this->registry->getEntityManager();
        $metadataFactory = $em->getMetadataFactory();
        $metadata = $metadataFactory->getMetadataFor(get_class($data));

        foreach ($metadata->getAssociationNames() as $property) {
            $value = $propertyAccessor->getValue($data, $property);

            if (!$value) {
                continue;
            }

            if ($value instanceof OwnableInterface && !$value->getOwner()) {
                $value->setOwner($owner);
            }
        }
    }
}
