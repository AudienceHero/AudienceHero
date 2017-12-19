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

namespace AudienceHero\Bundle\FileBundle\ETL;

use AudienceHero\Bundle\FileBundle\Entity\File;
use AudienceHero\Bundle\FileBundle\ETL\Step\Context;
use AudienceHero\Bundle\FileBundle\ETL\Step\StepInterface;

/**
 * ETL.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
class Workflow
{
    private $steps = [];

    public function addStep(StepInterface $step): void
    {
        $priority = $step->getPriority();

        if (isset($this->steps[$priority])) {
            $item = $this->steps[$priority];
            if (!is_array($item)) {
                $item = [$item];
            }
            $item[] = $step;
            $this->steps[$priority] = $item;
        } else {
            $this->steps[$step->getPriority()] = $step;
        }
    }

    public function run(File $file)
    {
        $context = new Context($file);
        $steps = $this->steps;
        ksort($steps);
        foreach ($steps as $step) {
            if (!$step->supports($context)) {
                continue;
            }

            $step->run($context);
        }

        @unlink($context->getPath());
    }
}
