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

use AudienceHero\Bundle\ActivityBundle\Enricher\ChainEnricher;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class EnricherCompiler implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->has(ChainEnricher::class)) {
            return;
        }

        $enricher = $container->getDefinition(ChainEnricher::class);
        $taggedServices = $container->findTaggedServiceIds('audiencehero_activity.enricher');
        foreach ($taggedServices as $id => $tags) {
            $enricher->addMethodCall('addEnricher', [new Reference($id)]);
        }
    }
}
