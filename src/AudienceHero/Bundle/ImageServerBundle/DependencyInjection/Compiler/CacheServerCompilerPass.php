<?php

namespace AudienceHero\Bundle\ImageServerBundle\DependencyInjection\Compiler;

use AudienceHero\Bundle\ImageServerBundle\Server\CacheServer;
use AudienceHero\Bundle\ImageServerBundle\Server\Server;
use AudienceHero\Bundle\ImageServerBundle\Server\ServerInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;

class CacheServerCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $cache = $container->getParameter('audiencehero_image_server.doctrine_cache');
        if (!$cache) {
            $container->setAlias(ServerInterface::class, Server::class);
            return;
        }

        try {
            $cacheDef = $container->getDefinition(sprintf('doctrine_cache.providers.%s', $cache));

            $cacheServer = $container->getDefinition(ServerInterface::class);
            $cacheServer->replaceArgument(1, $cacheDef);

            $container->setAlias(ServerInterface::class, CacheServer::class);
        } catch(ServiceNotFoundException $e) {
            throw new \RuntimeException(sprintf('There is no doctrine cache provider with key "%s". Please refer to the DoctrineCacheBundle documentation in order to configure your cache.', $cache), 0, $e);
        }
    }
}
