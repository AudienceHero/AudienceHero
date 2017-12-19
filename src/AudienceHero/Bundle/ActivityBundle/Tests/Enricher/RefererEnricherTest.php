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

namespace AudienceHero\Bundle\ActivityBundle\Tests\Enricher;

use AudienceHero\Bundle\ActivityBundle\Enricher\RefererEnricher;
use AudienceHero\Bundle\ActivityBundle\Entity\Activity;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Snowplow\RefererParser\Parser;
use Snowplow\RefererParser\Referer;

class RefererEnricherTest extends TestCase
{
    /** @var Parser */
    private $parser;

    public function setUp()
    {
        $this->parser = $this->prophesize(Parser::class);
    }

    public function testEnricherAbortsWithEmptyReferer()
    {
        $this->parser->parse(Argument::any())->shouldNotBeCAlled();
        $activity = new Activity();
        $enricher = new RefererEnricher($this->parser->reveal(), []);
        $enricher->enrich($activity);

        $this->assertNull($activity->getRefererMedium());
        $this->assertNull($activity->getRefererSource());
        $this->assertNull($activity->getRefererSearchTerm());
    }

    public function testEnricherSetsActivityAsSpamIfBlacklisted()
    {
        $this->parser->parse(Argument::any())->shouldNotBeCAlled();
        $activity = new Activity();
        $activity->setReferer('spam.example.com');

        $enricher = new RefererEnricher($this->parser->reveal(), ['spam.example.com']);
        $enricher->enrich($activity);

        $this->assertTrue($activity->getIsSpam());
        $this->assertNull($activity->getRefererMedium());
        $this->assertNull($activity->getRefererSource());
        $this->assertNull($activity->getRefererSearchTerm());
    }

    public function testEnricherSetActivityRefererFieldsIfRefererIsKnown()
    {
        $url = 'http://m.facebook.com/l.php?u=http%3A%2F%2Fwww.psychicbazaar.com%2Fblog%2F2012%2F09%2Fpsychic-bazaar-reviews-tarot-foundations-31-days-to-read-tarot-with-confidence%2F&h=kAQGXKbf9&s=1';
        $referer = Referer::createKnown('social', 'Facebook', '');
        $this->parser->parse($url)->shouldBeCalled()->willReturn($referer);
        $activity = new Activity();
        $activity->setReferer($url);

        $enricher = new RefererEnricher($this->parser->reveal(), []);
        $enricher->enrich($activity);

        $this->assertFalse($activity->getIsSpam());
        $this->assertEquals('social', $activity->getRefererMedium());
        $this->assertEquals('Facebook', $activity->getRefererSource());
        $this->assertNull($activity->getRefererSearchTerm());
    }

    public function testEnricherDoesNotSetActivityRefererFieldsIfRefererIsUnknown()
    {
        $url = 'unknown';
        $referer = Referer::createUnknown();
        $this->parser->parse($url)->shouldBeCalled()->willReturn($referer);
        $activity = new Activity();
        $activity->setReferer($url);

        $enricher = new RefererEnricher($this->parser->reveal(), []);
        $enricher->enrich($activity);

        $this->assertFalse($activity->getIsSpam());
        $this->assertNull($activity->getRefererMedium());
        $this->assertNull($activity->getRefererSource());
        $this->assertNull($activity->getRefererSearchTerm());
    }

    public function testEnricherSetsSearchTerms()
    {
        $url = 'http://www.google.com/search?q=gateway+oracle+cards+denise+linn&hl=en&client=safari';
        $referer = Referer::createKnown('search', 'Google', 'gateway oracle cards denise linn');

        $this->parser->parse($url)->shouldBeCalled()->willReturn($referer);
        $enricher = new RefererEnricher($this->parser->reveal(), []);

        $activity = new Activity();
        $activity->setReferer($url);

        $enricher->enrich($activity);

        $this->assertEquals('search', $activity->getRefererMedium());
        $this->assertEquals('Google', $activity->getRefererSource());
        $this->assertEquals('gateway oracle cards denise linn', $activity->getRefererSearchTerm());
    }
}
