<?php

/*
 * This file is part of the AudienceHero project.
 *
 * (c) Marc Weistroff <marc@weistroff.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AudienceHero\Bundle\PromoBundle\Linkable;

use AudienceHero\Bundle\PromoBundle\Entity\Promo;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class PromoLinkablePopulatorTest extends TestCase
{
    private $populator;
    private $router;

    public function setUp()
    {
        $this->router = $this->prophesize(UrlGeneratorInterface::class);
        $this->populator = new PromoLinkablePopulator($this->router->reveal());
    }

    public function testSupports()
    {
        $this->assertTrue($this->populator->supports(new Promo()));
    }

    public function testPopulate()
    {
        $promo = new Promo();
        $this->router->generate(Argument::any(), Argument::any(), UrlGeneratorInterface::ABSOLUTE_URL)
             ->shouldBeCalled()->willReturn('foobar');
        $this->populator->populate($promo);
        $this->assertSame($promo->getURL('preview'), 'foobar');
    }
}
