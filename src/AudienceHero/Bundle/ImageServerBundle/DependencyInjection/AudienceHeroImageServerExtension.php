<?php

namespace AudienceHero\Bundle\ImageServerBundle\DependencyInjection;

use AudienceHero\Bundle\ImageServerBundle\DependencyInjection\Compiler\ChainTransformerCompilerPass;
use AudienceHero\Bundle\ImageServerBundle\Server\Server;
use AudienceHero\Bundle\ImageServerBundle\Server\ServerInterface;
use AudienceHero\Bundle\ImageServerBundle\Transformer\TransformerInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\Intl\DateFormatter\DateFormat\Transformer;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * @link http://symfony.com/doc/current/cookbook/bundles/extension.html
 */
class AudienceHeroImageServerExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        $container->setAlias(ServerInterface::class, Server::class);

        $container->setParameter('audiencehero_image_server.doctrine_cache', $config['cache']);
        $container->registerForAutoconfiguration(TransformerInterface::class)
                  ->addTag(ChainTransformerCompilerPass::TAG);
    }
}
