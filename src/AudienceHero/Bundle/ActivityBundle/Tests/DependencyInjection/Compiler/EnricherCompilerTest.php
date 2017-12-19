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

namespace AudienceHero\Bundle\ActivityBundle\Tests\DependencyInjection\Compiler;

use AudienceHero\Bundle\ActivityBundle\DependencyInjection\Compiler\EnricherCompiler;
use AudienceHero\Bundle\ActivityBundle\Enricher\ChainEnricher;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * EnricherCompilerTest.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
class EnricherCompilerTest extends TestCase
{
    /** @var EnricherCompiler */
    private $compiler;

    public function setUp()
    {
        $this->compiler = new EnricherCompiler();
    }

    public function testCompilerStopsWhenEnricherIsNotPresent()
    {
        $container = $this->prophesize(ContainerBuilder::class);
        $container->has(ChainEnricher::class)->shouldBeCalled()->willReturn(false);
        $container->get(ChainEnricher::class)->shouldNotBeCalled();

        $this->compiler->process($container->reveal());
    }

    public function testCompilerRegistersTaggedServices()
    {
        $container = $this->prophesize(ContainerBuilder::class);
        $container->has(ChainEnricher::class)->shouldBeCalled()->willReturn(true);

        $collection = $this->prophesize(Definition::class);
        $container->getDefinition(ChainEnricher::class)->willReturn($collection)->shouldBeCalled();

        $container->findTaggedServiceIds('audiencehero_activity.enricher')
            ->willReturn(['id1' => [], 'id2' => []]);

        $collection->addMethodCall('addEnricher', Argument::that(function ($arguments) {
            /** @var Reference $argument */
            $argument = $arguments[0];

            return $argument instanceof Reference && in_array($argument->__toString(), ['id1', 'id2'], true);
        }))->shouldBeCalledTimes(2);
        $collection->reveal();

        $this->compiler->process($container->reveal());
    }
}
