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

namespace AudienceHero\Bundle\CoreBundle\Behavior\Linkable\DependencyInjection\Compiler;

use AudienceHero\Bundle\CoreBundle\Behavior\Linkable\Populator\LinkablePopulatorChain;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class LinkablePopulatorChainCompiler implements CompilerPassInterface
{
    public const TAG = 'audiencehero.core.behavior.linkable.populator';

    public function process(ContainerBuilder $container)
    {
        $populator = $container->getDefinition(LinkablePopulatorChain::class);

        $taggedServices = $container->findTaggedServiceIds(self::TAG);
        foreach ($taggedServices as $id => $tags) {
            $populator->addMethodCall('addPopulator', [new Reference($id)]);
        }
    }
}
