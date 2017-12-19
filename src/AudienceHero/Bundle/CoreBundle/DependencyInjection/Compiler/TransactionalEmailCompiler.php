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

use AudienceHero\Bundle\CoreBundle\Bridge\Sylius\Mailer\Provider\TransactionalEmailProvider;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class TransactionalEmailCompiler implements CompilerPassInterface
{
    public const TAG = 'audiencehero.core.mailer.email.transactional';

    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition(TransactionalEmailProvider::class)) {
            return;
        }

        $provider = $container->getDefinition(TransactionalEmailProvider::class);
        $taggedServices = $container->findTaggedServiceIds(self::TAG);
        foreach ($taggedServices as $id => $tags) {
            $provider->addMethodCall('registerEmail', [new Reference($id)]);
        }
    }
}
