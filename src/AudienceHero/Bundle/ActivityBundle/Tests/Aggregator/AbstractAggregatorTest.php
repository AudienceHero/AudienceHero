<?php

/*
 * This file is part of the AudienceHero project.
 *
 * (c) Marc Weistroff <marc@weistroff.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AudienceHero\Bundle\ActivityBundle\Tests\Aggregator;

use AudienceHero\Bundle\ActivityBundle\Aggregator\AbstractAggregator;
use AudienceHero\Bundle\ActivityBundle\Aggregator\AggregateComputer;
use AudienceHero\Bundle\ActivityBundle\Entity\Aggregate;
use PHPUnit\Framework\TestCase;

class AbstractAggregatorTest extends TestCase
{
    public function testAggregator()
    {
        $computer = $this->prophesize(AggregateComputer::class)->reveal();
        $aggregator = new class($computer) extends AbstractAggregator{
            public function supportsType(): string
            {
                return 'foo';
            }

            public function supportsClass(): string
            {
                return 'bar';
            }

            public function compute(Aggregate $aggregate): void
            {
            }
        };

        $this->assertSame($computer, $aggregator->getAggregateComputer());
    }
}
