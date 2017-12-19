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

namespace AudienceHero\Bundle\FileBundle\DependencyInjection;

use AudienceHero\Bundle\FileBundle\ETL\Step\StepInterface;
use AudienceHero\Bundle\FileBundle\Filesystem\FileSystemInterface;
use AudienceHero\Bundle\FileBundle\Filesystem\LocalFileSystem;
use AudienceHero\Bundle\FileBundle\Filesystem\NullFileSystem;
use AudienceHero\Bundle\FileBundle\Filesystem\S3FileSystem;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class AudienceHeroFileExtension extends Extension
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

        $container->registerForAutoconfiguration(StepInterface::class)
                  ->addTag('audiencehero_file.workflow.step');

        $s3FileSystem = $container->getDefinition(S3FileSystem::class);
        $localFileSystem = $container->getDefinition(LocalFileSystem::class);

        $s3FileSystem->setArgument(1, $container->getParameter('aws_upload_bucket'))
                     ->setArgument(2, $config['base']);

        $localFileSystem->setArgument(0, sprintf('%s/%s', $container->getParameter('kernel.project_dir'), 'web'))
                        ->setArgument(1, $config['base']);

        $filesystem = $config['filesystem'];
        if ('s3' === $filesystem) {
            $container->setAlias(FileSystemInterface::class, S3FileSystem::class);
        } elseif ('local' === $filesystem) {
            $container->setAlias(FileSystemInterface::class, LocalFileSystem::class);
        } elseif ('null' === $filesystem) {
            $container->setAlias(FileSystemInterface::class, NullFileSystem::class);
        }
    }
}
