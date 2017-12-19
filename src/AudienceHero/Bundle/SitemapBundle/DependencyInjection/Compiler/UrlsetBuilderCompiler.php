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

namespace AudienceHero\Bundle\SitemapBundle\DependencyInjection\Compiler;

use AudienceHero\Bundle\SitemapBundle\Builder\BuilderCollection;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class UrlsetBuilderCompiler implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->has(BuilderCollection::class)) {
            return;
        }

        $collection = $container->getDefinition(BuilderCollection::class);
        $tagged = $container->findTaggedServiceIds('audiencehero_sitemap.urlset_builder');
        foreach ($tagged as $id => $tags) {
            $collection->addMethodCall('addBuilder', [new Reference($id)]);
        }
    }
}
