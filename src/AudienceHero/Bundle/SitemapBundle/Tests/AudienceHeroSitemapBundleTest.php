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

namespace AudienceHero\Bundle\SitemapBundle\Tests;

use AudienceHero\Bundle\SitemapBundle\AudienceHeroSitemapBundle;
use AudienceHero\Bundle\SitemapBundle\DependencyInjection\Compiler\UrlsetBuilderCompiler;
use Prophecy\Argument;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class AudienceHeroSitemapBundleTest extends \PHPUnit_Framework_TestCase
{
    public function testBundleRegistersTheUrlsetCompilerPass()
    {
        $bundle = new AudienceHeroSitemapBundle();

        $container = $this->prophesize(ContainerBuilder::class);
        $container->addCompilerPass(Argument::type(UrlsetBuilderCompiler::class))->shouldBeCalled();

        $bundle->build($container->reveal());
    }
}
