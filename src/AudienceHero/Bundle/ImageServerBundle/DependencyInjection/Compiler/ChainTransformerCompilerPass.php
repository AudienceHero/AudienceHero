<?php

/*
 * This file is part of the AudienceHero project.
 *
 * (c) Marc Weistroff <marc@weistroff.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AudienceHero\Bundle\ImageServerBundle\DependencyInjection\Compiler;

use AudienceHero\Bundle\ImageServerBundle\Transformer\ChainTransformer;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * ChainTransformerCompilerPass
 * @author Marc Weistroff <marc@weistroff.net>
 */
class ChainTransformerCompilerPass implements CompilerPassInterface
{
    public const TAG = 'audiencehero.image_server.transformer';

    /**
     * You can modify the container here before it is dumped to PHP code.
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition(ChainTransformer::class)) {
            return;
        }

        $provider = $container->getDefinition(ChainTransformer::class);
        $taggedServices = $container->findTaggedServiceIds(self::TAG);
        foreach ($taggedServices as $id => $tags) {
            $provider->addMethodCall('addTransformer', [new Reference($id)]);
        }
    }
}