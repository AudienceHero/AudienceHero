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

use ApiPlatform\Core\Api\IriConverterInterface;
use ApiPlatform\Core\Bridge\Symfony\Routing\IriConverter;
use AudienceHero\Bundle\ActivityBundle\Repository\ActivityRepository;
use AudienceHero\Bundle\CoreBundle\Behavior\Identifiable\Finder\IdentifiableFinder;
use AudienceHero\Bundle\CoreBundle\Behavior\Identifiable\IdentifiableInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * AggregateComputer.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
class AggregateComputer
{
    /**
     * @var IdentifiableFinder
     * */
    private $identifiableFinder;
    /**
     * @var IriConverter
     */
    private $iriConverter;
    /**
     * @var ActivityRepository
     */
    private $repository;

    public function __construct(ActivityRepository $repository, IdentifiableFinder $identifiableFinder, IriConverterInterface $iriConverter)
    {
        $this->identifiableFinder = $identifiableFinder;
        $this->iriConverter = $iriConverter;
        $this->repository = $repository;
    }

    public function countTotal(string $class, string $subjectId, string $type): int
    {
        $subject = $this->getSubjectInstance($class, $subjectId);

        return $this->repository->countTotal($subject->getSoftReferenceKey(), $this->getIri($subject), $type);
    }

    public function countDaily(string $class, string $subjectId, string $type): array
    {
        $subject = $this->getSubjectInstance($class, $subjectId);

        return $this->repository->countDaily($subject->getSoftReferenceKey(), $this->getIri($subject), $type);
    }

    public function countField(string $class, string $subjectId, string $type, string $field, int $limit): array
    {
        $subject = $this->getSubjectInstance($class, $subjectId);

        return $this->repository->countField($subject->getSoftReferenceKey(), $this->getIri($subject), $type, $field, $limit);
    }

    private function getSubjectInstance(string $class, string $id): IdentifiableInterface
    {
        $subject = $this->identifiableFinder->find($class, $id);
        if (!$subject) {
            throw new \RuntimeException(sprintf('Cannot compute aggregate for non-existing instance of %s with id %s', $class, $id));
        }

        return $subject;
    }

    private function getIri($instance): ?string
    {
        return $this->iriConverter->getIriFromItem($instance);
    }
}
