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

namespace AudienceHero\Bundle\CoreBundle\DependencyInjection;

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
     * {@inheritdoc}
     *
     * @codeCoverageIgnore
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('audience_hero_core');

        $rootNode
            ->children()
                ->arrayNode('mailer')
                    ->isRequired()
                        ->children()
                            ->enumNode('adapter')->values(['mailgun', 'swiftmailer'])->cannotBeEmpty()->end()
                            ->arrayNode('mailgun')
                            ->isRequired()
                            ->children()
                                ->scalarNode('api_key')->cannotBeEmpty()->end()
                                ->scalarNode('domain')->cannotBeEmpty()->end()
                                ->booleanNode('is_test_delivery')->isRequired()->end()
                            ->end()
                        ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
