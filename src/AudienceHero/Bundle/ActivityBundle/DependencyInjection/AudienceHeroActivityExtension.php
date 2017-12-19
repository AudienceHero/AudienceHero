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

namespace AudienceHero\Bundle\ActivityBundle\DependencyInjection;

use AudienceHero\Bundle\ActivityBundle\Aggregator\AggregatorInterface;
use AudienceHero\Bundle\ActivityBundle\CollectionBuilder\EntityCollectionBuilderInterface;
use AudienceHero\Bundle\ActivityBundle\Enricher\DeviceEnricher;
use AudienceHero\Bundle\ActivityBundle\Enricher\ChainEnricher;
use AudienceHero\Bundle\ActivityBundle\Enricher\EnricherInterface;
use Doctrine\Common\Cache\FilesystemCache;
use Snowplow\RefererParser\Parser;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class AudienceHeroActivityExtension extends Extension
{
    /**
     * {@inheritdoc}
     *
     * @codeCoverageIgnore
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        $container->registerForAutoconfiguration(AggregatorInterface::class)
            ->addTag('audiencehero_activity.aggregator');

        $container->registerForAutoconfiguration(EnricherInterface::class)
                ->addTag('audiencehero_activity.enricher');

        $container->registerForAutoconfiguration(EntityCollectionBuilderInterface::class)
                  ->addTag('audiencehero_activity.collection_builder');

        $container->getDefinition(Parser::class)
                  ->replaceArgument(1, [
                      //$config['routing']['host_www'],
                      //$config['routing']['host_img'],
                  ]);

        $cacheId = 'doctrine_cache.providers.audiencehero_devicedetector';
        $cache = new Definition(FilesystemCache::class, [
            '%kernel.root_dir%/../var/cache/%kernel.environment%/device_detector',
            '.cache',
        ]);
        $container->setDefinition($cacheId, $cache);
        $container->getDefinition(DeviceEnricher::class)->setArgument(0, new Reference($cacheId));

        $container->setAlias(EnricherInterface::class, ChainEnricher::class);

        $cacheId = 'doctrine_cache.providers.audiencehero_geocoding';
        $cache = new Definition(FilesystemCache::class, [
            '%kernel.root_dir%/../var/cache/%kernel.environment%/geocoding',
            '.cache',
        ]);
        $container->setDefinition($cacheId, $cache);
        $container->getDefinition(DeviceEnricher::class)->setArgument(0, new Reference($cacheId));
    }
}
