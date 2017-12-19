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

namespace AudienceHero\Bundle\CoreBundle\Behavior\Linkable\Populator;

use AudienceHero\Bundle\CoreBundle\Behavior\Linkable\LinkableInterface;

/**
 * LocatablePopulatorInterface.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
interface LinkablePopulatorInterface
{
    public function supports(LinkableInterface $object): bool;
    public function populate(LinkableInterface $object);
}
