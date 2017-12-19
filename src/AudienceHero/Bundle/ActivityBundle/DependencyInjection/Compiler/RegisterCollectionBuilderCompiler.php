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

use AudienceHero\Bundle\ActivityBundle\CollectionBuilder\ChainEntityCollectionBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * RegisterCollectionBuilderPass.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
class RegisterCollectionBuilderCompiler implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->has(ChainEntityCollectionBuilder::class)) {
            return;
        }

        $definition = $container->getDefinition(ChainEntityCollectionBuilder::class);

        foreach ($container->findTaggedServiceIds('audiencehero_activity.collection_builder') as $id => $service) {
            $def = $container->getDefinition($id);
            $definition->addMethodCall('addCollectionBuilder', [$def]);
        }
    }
}
