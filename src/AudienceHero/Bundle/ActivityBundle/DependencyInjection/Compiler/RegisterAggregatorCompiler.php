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

namespace AudienceHero\Bundle\ActivityBundle\DependencyInjection\Compiler;

use AudienceHero\Bundle\ActivityBundle\Aggregator\Aggregator;
use AudienceHero\Bundle\ActivityBundle\Aggregator\ChainAggregator;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * RegisterAggregatorPass.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
class RegisterAggregatorCompiler implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->has(ChainAggregator::class)) {
            return;
        }

        $definition = $container->getDefinition(ChainAggregator::class);

        foreach ($container->findTaggedServiceIds('audiencehero_activity.aggregator') as $id => $aggregator) {
            $def = $container->getDefinition($id);
            $definition->addMethodCall('addAggregator', [$def]);
        }
    }
}
