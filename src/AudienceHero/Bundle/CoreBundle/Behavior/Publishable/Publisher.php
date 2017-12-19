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

namespace AudienceHero\Bundle\CoreBundle\Behavior\Publishable;

use AudienceHero\Bundle\CoreBundle\Behavior\Publishable\Repository\Specification\PublishableScheduledAndReady;
use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use Doctrine\ORM\EntityManagerInterface;
use Happyr\DoctrineSpecification\EntitySpecificationRepository;

/**
 * PublishableEntityManager.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
class Publisher
{
    /** @var EntityManagerInterface */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function publishScheduled(): void
    {
        /** @var ClassMetadata[] $metas */
        $metas = $this->em->getMetadataFactory()->getAllMetadata();

        foreach ($metas as $meta) {
            $rc = $meta->getReflectionClass();
            if (!$rc->implementsInterface(PublishableInterface::class)) {
                continue;
            }

            /** @var EntitySpecificationRepository $repository */
            $repository = $this->em->getRepository($meta->getName());
            $objects = $repository->match(new PublishableScheduledAndReady());
            foreach ($objects as $object) {
                $this->publish($object);
            }
        }

        $this->em->flush();
    }

    /**
     * Publish a single entity.
     *
     * TODO: Dispatch an event after the entity was published
     *
     * @param PublishableInterface $publishable
     */
    public function publish(PublishableInterface $publishable): void
    {
        if (!$publishable->isTimeForPublication()) {
            return;
        }

        $publishable->publish();
    }
}
