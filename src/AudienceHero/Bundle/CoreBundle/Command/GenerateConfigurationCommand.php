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

namespace AudienceHero\Bundle\CoreBundle\Command;

use AudienceHero\Bundle\CoreBundle\Behavior\Module\ModuleProviderInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * GenerateConfigurationCommand.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
class GenerateConfigurationCommand extends Command
{
    private $kernel;

    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('audiencehero:generate:configuration')
            ->setHelp('Generate configuration for single page applications');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $frontModules = [];
        $backModules = [];

        foreach ($this->kernel->getBundles() as $bundle) {
            if (!$bundle instanceof ModuleProviderInterface) {
                continue;
            }

            if ($import = $bundle->getFrontOfficeModule()) {
                $frontModules[$bundle->getName()] = $import;
            }

            if ($import = $bundle->getBackOfficeModule()) {
                $backModules[$bundle->getName()] = $import;
            }
        }
        ksort($frontModules);
        ksort($backModules);

        file_put_contents(
            sprintf('%s/Resources/spa/backoffice/configuration.js', $this->kernel->getRootDir()),
            $this->generateBackOfficeConfiguration($backModules)
        );

        file_put_contents(
            sprintf('%s/Resources/spa/frontoffice/configuration.js', $this->kernel->getRootDir()),
            $this->generateFrontOfficeConfiguration($frontModules)
        );
    }

    private function generateBackOfficeConfiguration(array $modules): string
    {
        // Import modules into javascript file
        $imports[] = 'import {flattenBundleProperty, flattenReducers} from \'@audiencehero/common\';';
        foreach ($modules as $bundle => $module) {
            $imports[] = sprintf("import {Bundle as %s} from '%s';", $bundle, $module);
        }

        $configuration = '';
        $configuration .= implode("\n", $imports)."\n";

        // Now put all modules into an array
        $configuration .= sprintf("const bundles = [%s];\n", implode(', ', array_keys($modules)));

        $configuration .= <<<EOF
export const customReducers = flattenReducers(bundles);
export const resources = flattenBundleProperty(bundles, 'resources');
export const menu = flattenBundleProperty(bundles, 'menu');
export const customRoutes = flattenBundleProperty(bundles, 'routes');
export const customSagas = flattenBundleProperty(bundles, 'sagas');
export const settingsMenu = flattenBundleProperty(bundles, 'settingsMenu');
export const importMenu = flattenBundleProperty(bundles, 'importMenu');
export const bundleMessages = flattenBundleProperty(bundles, 'messages');
EOF;

        return $configuration;
    }

    private function generateFrontOfficeConfiguration(array $modules): string
    {
        // Import modules into javascript file
        $imports[] = 'import {flattenBundleProperty, flattenReducers} from \'@audiencehero/common\';';
        foreach ($modules as $bundle => $module) {
            $imports[] = sprintf("import {Bundle as %s} from '%s';", $bundle, $module);
        }

        $configuration = '';
        $configuration .= implode("\n", $imports)."\n";

        // Now put all modules into an array
        $configuration .= sprintf("\nconst bundles = [\n    %s\n];\n", implode(",\n    ", array_keys($modules)));

        $configuration .= <<<EOF
        
export const reducers = flattenReducers(bundles);
export const routes = flattenBundleProperty(bundles, 'routes');
export const sagas = flattenBundleProperty(bundles, 'sagas');
export const bundleMessages = flattenBundleProperty(bundles, 'messages');
EOF;

        return $configuration;
    }
}
