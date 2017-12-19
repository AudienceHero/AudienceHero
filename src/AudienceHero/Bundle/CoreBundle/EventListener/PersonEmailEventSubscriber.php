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

use AudienceHero\Bundle\CoreBundle\Entity\Person;
use AudienceHero\Bundle\CoreBundle\Entity\PersonEmail;
use AudienceHero\Bundle\CoreBundle\Mailer\Model\PersonEmailVerificationEmail;
use AudienceHero\Bundle\CoreBundle\Mailer\TransactionalMailer;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;

/**
 * PersonEmailEventSubscriber.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
class PersonEmailEventSubscriber implements EventSubscriber
{
    /** @var TransactionalMailer */
    private $mailer;

    public function __construct(TransactionalMailer $mailer)
    {
        $this->mailer = $mailer;
    }

    public function getSubscribedEvents()
    {
        return [
            'prePersist',
            'postPersist',
        ];
    }

    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if (!$entity instanceof Person) {
            return;
        }

        $pe = new PersonEmail();
        $pe->setEmail($entity->getEmail());
        $pe->setIsVerified(false);
        $pe->setOwner($entity);

        $em = $args->getEntityManager();
        $em->persist($pe);
    }

    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        // We check for php_sapi_name so that email send is disabled when loading fixtures
        if (PHP_SAPI !== 'cli' && $entity instanceof PersonEmail && !$entity->getIsVerified()) {
            $this->mailer->send(PersonEmailVerificationEmail::class, $entity->getOwner(), ['owner' => $entity->getOwner(), 'person_email' => $entity], $entity->getEmail());
        }
    }
}
