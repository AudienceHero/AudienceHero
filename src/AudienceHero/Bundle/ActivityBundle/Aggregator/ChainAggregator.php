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

namespace AudienceHero\Bundle\ActivityBundle\Aggregator;

use AudienceHero\Bundle\ActivityBundle\Entity\Aggregate;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Aggregator.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
class ChainAggregator
{
    /** @var array */
    private $aggregators = [];

    /**
     * @var RegistryInterface
     */
    private $registry;

    public function __construct(RegistryInterface $registry, LoggerInterface $logger)
    {
        $this->registry = $registry;
        $this->logger = $logger;
    }

    public function addAggregator(AggregatorInterface $aggregator): void
    {
        $this->aggregators[] = $aggregator;
    }

    public function compute(string $id, string $type): void
    {
        $em = $this->registry->getManager();
        $aggregate = $em->getRepository(Aggregate::class)->findOrCreateOneBySubjectIdAndType($id, $type);

        /** @var AggregatorInterface $aggregator */
        foreach ($this->aggregators as $aggregator) {
            try {
                if ($aggregator->supportsType() !== $type) {
                    continue;
                }

                if (!$aggregate->getOwner()) {
                    $subject = $em->find($aggregator->supportsClass(), $id);
                    if (!$subject) {
                        // If the subject has been deleted, there's not point to aggregate anything. We skip.
                        continue;
                    }

                    $aggregate->setOwner($subject->getOwner());
                }

                $aggregator->compute($aggregate);
            } catch (\Exception $e) {
                $this->logger->warning('Could not compute aggregate.', [
                    'subjectId' => $aggregate->getSubjectId(),
                    'type' => $aggregate->getType(),
                    'aggregator' => $aggregator,
                    'aggregate' => $aggregate,
                ]);
            }
        }

        if (!$em->contains($aggregate)) {
            $em->persist($aggregate);
        }

        $em->flush();
        $em->clear();
    }
}
