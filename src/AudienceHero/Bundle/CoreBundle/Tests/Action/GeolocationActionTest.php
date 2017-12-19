<?php

/*
 * This file is part of the AudienceHero project.
 *
 * (c) Marc Weistroff <marc@weistroff.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AudienceHero\Bundle\CoreBundle\Tests\Action;

use AudienceHero\Bundle\CoreBundle\Action\GeolocationAction;
use Geocoder\Geocoder;
use Geocoder\Model\Address;
use Geocoder\Model\AddressCollection;
use Geocoder\Model\AdminLevelCollection;
use Geocoder\Model\Country;
use Geocoder\Query\GeocodeQuery;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class GeolocationActionTest extends TestCase
{
    /** @var ObjectProphecy */
    private $geocoder;

    public function setUp()
    {
        $this->geocoder = $this->prophesize(Geocoder::class);
    }

    public function testInvoke()
    {
        $request = $this->prophesize(Request::class);
        $request->getClientIp()->shouldBeCalled()->willReturn('214.214.214.214');

        $addressCollection = new AddressCollection([
            new Address(
                'foo',
                new AdminLevelCollection(),
                null,
                null,
                null,
                null,
                null,
                "Lyon",
                null,
                new Country("France", "FR")
            )
        ]);

        $this->geocoder->geocodeQuery(Argument::that(function(GeocodeQuery $query) {
            return $query->getText() === '214.214.214.214';
        }))
            ->shouldBeCalled()
            ->willReturn($addressCollection)
        ;
        $action = new GeolocationAction($this->geocoder->reveal());
        $response = $action($request->reveal());
        $this->assertInstanceOf(Response::class, $response);
        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('application/ld+json', $response->headers->get('Content-Type'));
        $this->assertSame('{"city":"Lyon","country":"FR"}', $response->getContent());
    }
}
