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

namespace AudienceHero\Bundle\CoreBundle\Action;

use Geocoder\Geocoder;
use Geocoder\Query\GeocodeQuery;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * GeolocationAction.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
class GeolocationAction
{
    public function __construct(Geocoder $geocoder)
    {
        $this->geocoder = $geocoder;
    }

    /**
     * @Route("/api/geolocation")
     * @Method("GET")
     *
     * @throws \Geocoder\Exception\Exception
     */
    public function __invoke(Request $request)
    {
        $result = $this->geocoder->geocodeQuery(
            GeocodeQuery::create($request->getClientIp())
        );

        $geolocation = [
            'city' => null,
            'country' => null,
        ];

        if (0 !== count($geolocation)) {
            $geolocation['country'] = $result->first()->getCountry()->getCode();
            $geolocation['city'] = $result->first()->getLocality();
        }

        return new Response(json_encode($geolocation), 200, ['Content-Type' => 'application/ld+json']);
    }
}
