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

namespace AudienceHero\Bundle\SitemapBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Configuration.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
class Configuration implements ConfigurationInterface
{
    /**
     * Generates the configuration tree builder.
     *
     * @return \Symfony\Component\Config\Definition\Builder\TreeBuilder The tree builder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('audience_hero_file');

        $rootNode
            ->children()
                ->enumNode('writer')->defaultValue('file')->values(['memory', 'file'])->end()
                ->scalarNode('route')->defaultValue('homepage')->end()
                ->scalarNode('dir')->defaultValue('%kernel.root_dir%/../web')->end()
            ->end();

        return $treeBuilder;
    }
}
