<?php

/*
 * This file is part of the AudienceHero project.
 *
 * (c) Marc Weistroff <marc@weistroff.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AudienceHero\Bundle\AcquisitionFreeDownloadBundle\Tests\Bridge\AudienceHero\ActivityBundle\Aggregator;

use AudienceHero\Bundle\AcquisitionFreeDownloadBundle\Bridge\AudienceHero\ActivityBundle\Aggregator\UnlockAggregator;
use AudienceHero\Bundle\AcquisitionFreeDownloadBundle\Entity\AcquisitionFreeDownload;
use AudienceHero\Bundle\AcquisitionFreeDownloadBundle\Events\AcquisitionFreeDownloadEvents;
use AudienceHero\Bundle\ActivityBundle\Aggregator\AggregateComputer;
use AudienceHero\Bundle\ActivityBundle\Entity\Aggregate;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;

class UnlockAggregatorTest extends TestCase
{
    /** @var ObjectProphecy */
    private $computer;

    public function setUp()
    {
        $this->computer = $this->prophesize(AggregateComputer::class);
    }

    public function getInstance(): UnlockAggregator
    {
        return new UnlockAggregator($this->computer->reveal());
    }

    public function testSupportsType()
    {
        $this->assertSame(AcquisitionFreeDownloadEvents::UNLOCK, $this->getInstance()->supportsType());
    }

    public function testSupportsClass()
    {
        $this->assertSame(AcquisitionFreeDownload::class, $this->getInstance()->supportsClass());
    }

    public function testCompute()
    {
        $aggregate = $this->prophesize(Aggregate::class);

        $aggregate->getSubjectId()->shouldBeCalledTimes(2)->willReturn('id');
        $aggregate->getType()->shouldBeCalledTimes(2)->willReturn('type');

        $aggregate->addData(UnlockAggregator::AGGREGATE_TOTAL, 10) ->shouldBeCalled();
        $aggregate->addData(UnlockAggregator::AGGREGATE_DAILY, [20]) ->shouldBeCalled();

        $this->computer->countTotal(AcquisitionFreeDownload::class, 'id', 'type')->shouldBeCalled()->willReturn(10);
        $this->computer->countDaily(AcquisitionFreeDownload::class, 'id', 'type')->shouldBeCalled()->willReturn([20]);

        $this->getInstance()->compute($aggregate->reveal());
    }
}
