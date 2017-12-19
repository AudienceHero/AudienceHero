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

namespace AudienceHero\Bundle\ActivityBundle\Enricher;

use AudienceHero\Bundle\ActivityBundle\Entity\Activity;
use Geocoder\Geocoder;
use Geocoder\Query\GeocodeQuery;

class GeolocationEnricher implements EnricherInterface
{
    private $geocoder;

    public function __construct(Geocoder $geocoder)
    {
        $this->geocoder = $geocoder;
    }

    public function enrich(Activity $activity): void
    {
        if (!$activity->getIp()) {
            return;
        }

        $ip = $activity->getIp();
        $result = $this->geocoder->geocodeQuery(GeocodeQuery::create($ip));
        if (0 === count($result)) {
            return;
        }
        $address = $result->first();

        $activity->setCity($address->getLocality());
        $activity->setCountry($address->getCountry()->getCode());
        $activity->setRegion($address->getSubLocality());
        $activity->setLatitude($address->getCoordinates()->getLatitude());
        $activity->setLongitude($address->getCoordinates()->getLongitude());
    }
}
