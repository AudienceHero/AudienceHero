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

namespace AudienceHero\Bundle\CoreBundle\DependencyInjection\Compiler;

use AudienceHero\Bundle\CoreBundle\Bridge\Sylius\Mailer\Provider\ChainEmailProvider;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class SyliusEmailProviderCompiler implements CompilerPassInterface
{
    public const TAG = 'audiencehero.core.bridge.sylius.email_provider';

    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition(ChainEmailProvider::class)) {
            return;
        }

        $taggedServices = $container->findTaggedServiceIds(self::TAG);
        $chainEmailProvider = $container->getDefinition(ChainEmailProvider::class);
        foreach ($taggedServices as $id => $tags) {
            $chainEmailProvider->addMethodCall('registerProvider', [new Reference($id)]);
        }

        $container->getDefinition('sylius.email_sender')
            ->replaceArgument(2, new Reference(ChainEmailProvider::class));
    }
}
