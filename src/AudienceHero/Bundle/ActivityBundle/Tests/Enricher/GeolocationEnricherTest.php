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

use AudienceHero\Bundle\ActivityBundle\Enricher\GeolocationEnricher;
use AudienceHero\Bundle\ActivityBundle\Entity\Activity;
use Geocoder\Geocoder;
use Geocoder\Model\Address;
use Geocoder\Model\AddressCollection;
use Geocoder\Model\AdminLevelCollection;
use Geocoder\Model\Coordinates;
use Geocoder\Model\Country;
use Geocoder\Query\GeocodeQuery;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;

class GeolocationEnricherTest extends TestCase
{
    /** @var ObjectProphecy */
    private $geocoder;

    public function setUp()
    {
        $this->geocoder = $this->prophesize(Geocoder::class);
    }

    public function testGeolocationEnricherWithNullIP()
    {
        $this->geocoder->geocodeQuery(Argument::any())->shouldNotBeCalled();
        $enricher = new GeolocationEnricher($this->geocoder->reveal());
        $activity = new Activity();
        $enricher->enrich($activity);

        $this->assertNull($activity->getCity());
        $this->assertNull($activity->getCountry());
        $this->assertNull($activity->getRegion());
        $this->assertNull($activity->getLatitude());
        $this->assertNull($activity->getLongitude());
    }

    public function testGeolocationEnricherWithIP()
    {
        $ip = '128.79.246.206';
        $this->geocoder->geocodeQuery(Argument::that(function ($query) use ($ip) {
            return $query instanceof GeocodeQuery && $query->getText() === $ip;
        }))->shouldBeCalled()->willReturn($this->getAddressCollection());

        $enricher = new GeolocationEnricher($this->geocoder->reveal());
        $activity = new Activity();
        $activity->setIp($ip);
        $enricher->enrich($activity);

        $this->assertEquals('FR', $activity->getCountry());
        $this->assertEquals('Lyon', $activity->getCity());
        $this->assertEquals('Rhône', $activity->getRegion());
        $this->assertEquals(45, floor($activity->getLatitude()));
        $this->assertEquals(4, floor($activity->getLongitude()));
    }

    private function getAddressCollection(): AddressCollection
    {
        $address = new Address(
            'test',
            new AdminLevelCollection([]),
            new Coordinates(45, 4),
            null,
            null,
            null,
            '69006',
            'Lyon',
            'Rhône',
            new Country('France', 'FR')
        );

        return new AddressCollection([$address]);
    }
}
