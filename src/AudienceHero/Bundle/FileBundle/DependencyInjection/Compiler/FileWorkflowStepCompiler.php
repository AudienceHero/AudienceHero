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

namespace AudienceHero\Bundle\FileBundle\DependencyInjection\Compiler;

use AudienceHero\Bundle\FileBundle\ETL\Workflow;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class FileWorkflowStepCompiler implements CompilerPassInterface
{
    public const TAG = 'audiencehero_file.workflow.step';

    public function process(ContainerBuilder $container)
    {
        if (!$container->has(Workflow::class)) {
            return;
        }

        $workflow = $container->getDefinition(Workflow::class);
        $taggedServices = $container->findTaggedServiceIds(self::TAG);
        foreach ($taggedServices as $id => $tags) {
            $workflow->addMethodCall('addStep', [new Reference($id)]);
        }
    }
}
