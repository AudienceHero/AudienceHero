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

namespace AudienceHero\Bundle\FileBundle\ETL\Step;

/**
 * StepInterface.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
interface StepInterface
{
    const PRIORITY_FIRST = 0;
    const PRIORITY_NORMAL = 128;
    const PRIORITY_LAST = 255;

    public function run(Context $context): void;

    public function supports(Context $context): bool;

    public function getPriority(): int;
}
