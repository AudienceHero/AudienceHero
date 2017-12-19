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
 * LocatablePopulator.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
class LinkablePopulatorChain
{
    /** @var LinkablePopulatorInterface[] */
    private $populators = [];

    public function addPopulator(LinkablePopulatorInterface $populator): void
    {
        $this->populators[] = $populator;
    }

    public function populate(LinkableInterface $entity): void
    {
        foreach ($this->populators as $populator) {
            if (!$populator->supports($entity)) {
                continue;
            }

            $populator->populate($entity);
        }
    }
}
