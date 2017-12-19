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

namespace AudienceHero\Bundle\SitemapBundle\DependencyInjection;

use AudienceHero\Bundle\SitemapBundle\Builder\UrlsetBuilderInterface;
use AudienceHero\Bundle\SitemapBundle\Sitemap\Sitemap;
use AudienceHero\Bundle\SitemapBundle\Writer\FileWriter;
use AudienceHero\Bundle\SitemapBundle\Writer\MemoryWriter;
use AudienceHero\Bundle\SitemapBundle\Writer\WriterInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class AudienceHeroSitemapExtension extends Extension
{
    /**
     * {@inheritdoc}
     *
     * @codeCoverageIgnore
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        $container->registerForAutoconfiguration(UrlsetBuilderInterface::class)
                  ->addTag('audiencehero_sitemap.urlset_builder');

        if ('memory' === $config['writer']) {
            $container->setAlias(WriterInterface::class, MemoryWriter::class);
        } else {
            $container->setAlias(WriterInterface::class, FileWriter::class);
        }

        $container->getDefinition(FileWriter::class)->setArgument(0, $config['dir']);
        $container->getDefinition(Sitemap::class)
            ->setArgument(0, new Reference(WriterInterface::class))
            ->setArgument(1, $config['route'])
            ->setArgument(2, new Reference('router'))
        ;
    }
}
