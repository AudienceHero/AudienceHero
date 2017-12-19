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

namespace AudienceHero\Bundle\ActivityBundle;

use AudienceHero\Bundle\ActivityBundle\DependencyInjection\Compiler\EnricherCompiler;
use AudienceHero\Bundle\ActivityBundle\DependencyInjection\Compiler\RegisterAggregatorCompiler;
use AudienceHero\Bundle\ActivityBundle\DependencyInjection\Compiler\RegisterCollectionBuilderCompiler;
use AudienceHero\Bundle\CoreBundle\Behavior\Module\ModuleProviderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * ActivityBundle.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
class AudienceHeroActivityBundle extends Bundle implements ModuleProviderInterface
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new EnricherCompiler());
        $container->addCompilerPass(new RegisterAggregatorCompiler());
        $container->addCompilerPass(new RegisterCollectionBuilderCompiler());
    }

    /**
     * Returns the name of the javascript module to register for the Backoffice application.
     * Return null in case you don't have any back office module to register.
     */
    public function getBackOfficeModule(): ?string
    {
        return '@audiencehero-backoffice/activity';
    }

    /**
     * Returns the name of the javascript module to register for the Backoffice application.
     * Return null in case you don't have any front office module to register.
     */
    public function getFrontOfficeModule(): ?string
    {
        return '@audiencehero-frontoffice/activity';
    }
}
