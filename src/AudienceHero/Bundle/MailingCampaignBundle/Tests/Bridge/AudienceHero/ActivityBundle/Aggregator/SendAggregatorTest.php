<?php

/*
 * This file is part of the AudienceHero project.
 *
 * (c) Marc Weistroff <marc@weistroff.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AudienceHero\Bundle\MailingCampaignBundle\Tests\Bridge\AudienceHero\ActivityBundle\Aggregator;

use AudienceHero\Bundle\ActivityBundle\Aggregator\AbstractAggregator;
use AudienceHero\Bundle\ActivityBundle\Aggregator\AggregateComputer;
use AudienceHero\Bundle\ActivityBundle\Entity\Aggregate;
use AudienceHero\Bundle\MailingCampaignBundle\Bridge\AudienceHero\ActivityBundle\Aggregator\SendAggregator;
use AudienceHero\Bundle\MailingCampaignBundle\Entity\EmailEvent;
use AudienceHero\Bundle\MailingCampaignBundle\Entity\Mailing;
use PHPUnit\Framework\TestCase;

class SendAggregatorTest extends TestCase
{
    private $computer;
    private $aggregator;

    public function setUp()
    {
        $this->computer = $this->prophesize(AggregateComputer::class);
        $this->aggregator = new SendAggregator($this->computer->reveal());
    }

    public function testSupportsType()
    {
        $this->assertSame(EmailEvent::EVENT_SEND, $this->aggregator->supportsType());
    }

    public function testSupportsClass()
    {
        $this->assertSame(Mailing::class, $this->aggregator->supportsClass());
    }

    public function testCompute()
    {
        $aggregate = new Aggregate();
        $aggregate->setSubjectId('id');
        $this->computer->countTotal(
            Mailing::class,
            'id',
            EmailEvent::EVENT_SEND
        )->shouldBeCalled()->willReturn(10);

        $this->aggregator->compute($aggregate);

        $this->assertSame(
            10,
            $aggregate->getData()[AbstractAggregator::AGGREGATE_TOTAL]
        );
    }
}
