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

namespace AudienceHero\Bundle\ContactBundle\EventListener;

use AudienceHero\Bundle\ContactBundle\Entity\ContactsGroupForm;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Geocoder\Geocoder;
use Geocoder\Query\GeocodeQuery;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * FormGeocoderEventSubscriber.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
class FormGeocoderEventSubscriber implements EventSubscriber
{
    /**
     * @var RequestStack
     */
    private $requestStack;
    /**
     * @var Geocoder
     */
    private $geocoder;

    public function __construct(RequestStack $requestStack, Geocoder $geocoder)
    {
        $this->requestStack = $requestStack;
        $this->geocoder = $geocoder;
    }

    /**
     * Returns an array of events this subscriber wants to listen to.
     *
     * @return array
     */
    public function getSubscribedEvents()
    {
        return [
            'postLoad',
        ];
    }

    public function postLoad(LifecycleEventArgs $eventArgs)
    {
        $entity = $eventArgs->getEntity();
        if (!$entity instanceof ContactsGroupForm) {
            return;
        }

        $request = $this->requestStack->getMasterRequest();
        if ($request) {
            $result = $this->geocoder->geocodeQuery(
                GeocodeQuery::create($request->getClientIp())
            );

            if (0 !== count($result)) {
                $entity->setGuessedCountry($result->first()->getCountry()->getCode());
                $entity->setGuessedCity($result->first()->getLocality());
            }
        }
    }
}
