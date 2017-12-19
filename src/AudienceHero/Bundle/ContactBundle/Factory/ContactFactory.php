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

namespace AudienceHero\Bundle\ContactBundle\Factory;

use ApiPlatform\Core\Bridge\Symfony\Validator\Exception\ValidationException;
use AudienceHero\Bundle\ContactBundle\Entity\Contact;
use AudienceHero\Bundle\CoreBundle\Entity\Person;
use Geocoder\Geocoder;
use Geocoder\Query\GeocodeQuery;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * ContactFactory.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
class ContactFactory
{
    /**
     * @var RequestStack
     */
    private $requestStack;
    /**
     * @var Geocoder
     */
    private $geocoder;
    /**
     * @var SerializerInterface
     */
    private $serializer;
    /**
     * @var ValidatorInterface
     */
    private $validator;

    public function __construct(RequestStack $requestStack, Geocoder $geocoder, SerializerInterface $serializer, ValidatorInterface $validator)
    {
        $this->requestStack = $requestStack;
        $this->geocoder = $geocoder;
        $this->serializer = $serializer;
        $this->validator = $validator;
    }

    public function createGeolocated(Person $owner): Contact
    {
        $contact = new Contact();
        $contact->setOwner($owner);

        $request = $this->requestStack->getMasterRequest();
        if ($request) {
            $result = $this->geocoder->geocodeQuery(
                GeocodeQuery::create($request->getClientIp())
            );

            if (0 !== count($result)) {
                $contact->setCountry($result->first()->getCountry()->getCode());
                $contact->setCity($result->first()->getLocality());
            }
        }

        return $contact;
    }

    public function createFromJson(string $json, Person $owner): Contact
    {
        /** @var Contact $unserialized */
        $contact = $this->serializer->deserialize($json, Contact::class, 'json', [
            'item_operation_name' => 'put',
            'operation_type' => 'item',
            'api_allow_update' => false,
            'resource_class' => Contact::class,
            'groups' => ['write'],
        ]);
        $contact->setOwner($owner);

        $list = $this->validator->validate($contact, null, ['optin']);
        if ($list->count() > 0) {
            throw new ValidationException($list);
        }

        return $contact;
    }
}
