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

namespace AudienceHero\Bundle\SitemapBundle\Builder;

/**
 * BuilderCollection.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
class BuilderCollection
{
    private $builders = [];

    public function addBuilder(UrlsetBuilderInterface $builder)
    {
        $this->builders[$builder->getName()] = $builder;
    }

    /**
     * @return array
     */
    public function getBuilders(): array
    {
        return $this->builders;
    }
}
