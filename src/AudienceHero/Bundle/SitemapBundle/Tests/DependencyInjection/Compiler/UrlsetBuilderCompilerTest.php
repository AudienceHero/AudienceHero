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

namespace AudienceHero\Bundle\SitemapBundle\Tests\DependencyInjection\Compiler;

use AudienceHero\Bundle\SitemapBundle\Builder\BuilderCollection;
use AudienceHero\Bundle\SitemapBundle\DependencyInjection\Compiler\UrlsetBuilderCompiler;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class UrlsetBuilderCompilerTest extends TestCase
{
    /** @var UrlsetBuilderCompiler */
    private $compiler;

    public function setUp()
    {
        $this->compiler = new UrlsetBuilderCompiler();
    }

    public function testCompilerStopsWhenBuilderCollectionIsNotPresent()
    {
        $container = $this->prophesize(ContainerBuilder::class);
        $container->has(BuilderCollection::class)->shouldBeCalled()->willReturn(false);
        $container->get(BuilderCollection::class)->shouldNotBeCalled();

        $this->compiler->process($container->reveal());
    }

    public function testCompilerRegistersTaggedServices()
    {
        $container = $this->prophesize(ContainerBuilder::class);
        $container->has(BuilderCollection::class)->shouldBeCalled()->willReturn(true);

        $collection = $this->prophesize(Definition::class);
        $container->getDefinition(BuilderCollection::class)->willReturn($collection)->shouldBeCalled();

        $container->findTaggedServiceIds('audiencehero_sitemap.urlset_builder')
                  ->willReturn(['id1' => [], 'id2' => []]);

        $collection->addMethodCall('addBuilder', Argument::that(function ($arguments) {
            /** @var Reference $argument */
            $argument = $arguments[0];

            return $argument instanceof Reference && in_array($argument->__toString(), ['id1', 'id2'], true);
        }))->shouldBeCalledTimes(2);
        $collection->reveal();

        $this->compiler->process($container->reveal());
    }
}
