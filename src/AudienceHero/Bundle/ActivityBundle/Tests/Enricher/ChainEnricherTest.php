<?php

/*
 * This file is part of the AudienceHero project.
 *
 * (c) Marc Weistroff <marc@weistroff.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AudienceHero\Bundle\ActivityBundle\Tests\Enricher;

use AudienceHero\Bundle\ActivityBundle\Enricher\ChainEnricher;
use AudienceHero\Bundle\ActivityBundle\Enricher\EnricherInterface;
use AudienceHero\Bundle\ActivityBundle\Entity\Activity;
use PHPUnit\Framework\TestCase;

class ChainEnricherTest extends TestCase
{
    public function testEnrich()
    {
        $activity = new Activity();

        $e1 = $this->prophesize(EnricherInterface::class);
        $e2 = $this->prophesize(EnricherInterface::class);

        $e1->enrich($activity)->shouldBeCalled();
        $e2->enrich($activity)->shouldBeCalled();

        $chain = new ChainEnricher();
        $chain->addEnricher($e1->reveal());
        $chain->addEnricher($e2->reveal());

        $chain->enrich($activity);
    }
}
