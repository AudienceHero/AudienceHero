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

namespace AudienceHero\Bundle\FileBundle;

use AudienceHero\Bundle\CoreBundle\Behavior\Module\ModuleProviderInterface;
use AudienceHero\Bundle\FileBundle\DependencyInjection\Compiler\FileWorkflowStepCompiler;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class AudienceHeroFileBundle extends Bundle implements ModuleProviderInterface
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new FileWorkflowStepCompiler());
    }

    /**
     * Returns the name of the javascript module to register for the Backoffice application.
     * Return null in case you don't have any back office module to register.
     */
    public function getBackOfficeModule(): ?string
    {
        return '@audiencehero-backoffice/file';
    }

    /**
     * Returns the name of the javascript module to register for the Backoffice application.
     * Return null in case you don't have any front office module to register.
     */
    public function getFrontOfficeModule(): ?string
    {
        return '@audiencehero-frontoffice/file';
    }
}
