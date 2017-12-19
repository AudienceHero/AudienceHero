<?php

/*
 * This file is part of the AudienceHero project.
 *
 * (c) Marc Weistroff <marc@weistroff.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AudienceHero\Bundle\CoreBundle\Behavior\Identifiable\Finder;
use AudienceHero\Bundle\CoreBundle\Behavior\Identifiable\IdentifiableInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * IdentifiableFinder
 * @author Marc Weistroff <marc@weistroff.net>
 */
class IdentifiableFinder
{
    /**
     * @var RegistryInterface
     */
    private $registry;

    public function __construct(RegistryInterface $registry)
    {
        $this->registry = $registry;
    }

    /**
     * @param string $class
     * @param string $id
     *
     * @return IdentifiableInterface|null
     */
    public function find(string $class, string $id): ?IdentifiableInterface
    {
        return $this->registry->getRepository($class)->find($id);
    }
}