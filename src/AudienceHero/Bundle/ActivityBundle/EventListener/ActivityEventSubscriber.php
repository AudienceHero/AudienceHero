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

use AudienceHero\Bundle\ActivityBundle\Entity\Activity;
use AudienceHero\Bundle\ActivityBundle\Queue\ActivityProducer;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Psr\Log\LoggerInterface;

/**
 * ActivityEventSubscriber.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
class ActivityEventSubscriber implements EventSubscriber
{
    /**
     * @var ActivityProducer
     */
    private $producer;

    /** @var array */
    private $activities = [];

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(ActivityProducer $producer, LoggerInterface $logger)
    {
        $this->producer = $producer;
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function getSubscribedEvents()
    {
        return ['onFlush', 'onPostFlush'];
    }

    public function onFlush(OnFlushEventArgs $args): void
    {
        $em = $args->getEntityManager();
        $uow = $em->getUnitOfWork();

        foreach ($uow->getScheduledEntityInsertions() as $entity) {
            if (!$entity instanceof Activity) {
                return;
            }

            $this->activities[] = $entity;
        }
    }

    public function postFlush(PostFlushEventArgs $args): void
    {
        foreach ($this->activities as $activity) {
            try {
                $this->producer->enrich($activity);
            } catch (\Exception $e) {
                $this->logger->critical($e->getMessage(), ['e' => $e]);
            }
        }
    }
}
