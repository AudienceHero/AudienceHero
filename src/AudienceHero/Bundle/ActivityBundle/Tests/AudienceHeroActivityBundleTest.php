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

namespace AudienceHero\Bundle\ActivityBundle\Tests;

use AudienceHero\Bundle\ActivityBundle\AudienceHeroActivityBundle;
use AudienceHero\Bundle\ActivityBundle\DependencyInjection\Compiler\EnricherCompiler;
use AudienceHero\Bundle\ActivityBundle\DependencyInjection\Compiler\RegisterAggregatorCompiler;
use AudienceHero\Bundle\ActivityBundle\DependencyInjection\Compiler\RegisterCollectionBuilderCompiler;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class AudienceHeroActivityBundleTest extends TestCase
{
    public function testEnricherCompilerPassIsRegistered()
    {
        $container = $this->prophesize(ContainerBuilder::class);
        $container->addCompilerPass(Argument::type(EnricherCompiler::class))->shouldBeCalled();
        $container->addCompilerPass(Argument::type(RegisterAggregatorCompiler::class))->shouldBeCalled();
        $container->addCompilerPass(Argument::type(RegisterCollectionBuilderCompiler::class))->shouldBeCalled();

        $bundle = new AudienceHeroActivityBundle();
        $bundle->build($container->reveal());
    }
}
