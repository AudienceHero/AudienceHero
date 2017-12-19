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

use AudienceHero\Bundle\AcquisitionFreeDownloadBundle\Bridge\AudienceHero\ActivityBundle\Aggregator\HitAggregator;
use AudienceHero\Bundle\AcquisitionFreeDownloadBundle\Entity\AcquisitionFreeDownload;
use AudienceHero\Bundle\AcquisitionFreeDownloadBundle\Events\AcquisitionFreeDownloadEvents;
use AudienceHero\Bundle\ActivityBundle\Aggregator\AbstractAggregator;
use AudienceHero\Bundle\ActivityBundle\Aggregator\AggregateComputer;
use AudienceHero\Bundle\ActivityBundle\Entity\Aggregate;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;

class HitAggregatorTest extends TestCase
{
    /** @var ObjectProphecy */
    private $computer;

    public function setUp()
    {
        $this->computer = $this->prophesize(AggregateComputer::class);
    }

    private function getInstance(): HitAggregator
    {
        return new HitAggregator($this->computer->reveal());
    }

    public function testSupportsType()
    {
        $this->assertSame(AcquisitionFreeDownloadEvents::HIT, $this->getInstance()->supportsType());
    }

    public function testSupportsClass()
    {
        $this->assertSame(AcquisitionFreeDownload::class, $this->getInstance()->supportsClass());
    }

    public function testCompute()
    {
        $aggregate = $this->prophesize(Aggregate::class);
        $aggregate->getSubjectId()->willReturn('id')->shouldBeCalledTimes(3);
        $aggregate->getType()->willReturn('type')->shouldBeCalledTimes(3);

        $this->computer->countTotal(AcquisitionFreeDownload::class, 'id', 'type')->shouldBeCalled()->willReturn(10);
        $this->computer->countDaily(AcquisitionFreeDownload::class, 'id', 'type')->shouldBeCalled()->willReturn([20]);
        $this->computer->countField(AcquisitionFreeDownload::class, 'id', 'type', 'country',10)->willReturn(['FR' => 30]);

        $aggregate->addData(AbstractAggregator::AGGREGATE_TOTAL, 10)->shouldBeCalled();
        $aggregate->addData(AbstractAggregator::AGGREGATE_DAILY, [20])->shouldBeCalled();
        $aggregate->addData(AbstractAggregator::AGGREGATE_TOP10_COUNTRY, ['FR' => 30])->shouldBeCalled();

        $this->getInstance()->compute($aggregate->reveal());
    }
}
