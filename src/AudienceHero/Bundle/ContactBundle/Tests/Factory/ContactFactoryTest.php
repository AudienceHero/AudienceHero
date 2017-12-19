<?php

/*
 * This file is part of the AudienceHero project.
 *
 * (c) Marc Weistroff <marc@weistroff.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AudienceHero\Bundle\ContactBundle\Tests\Factory;

use AudienceHero\Bundle\ContactBundle\Entity\Contact;
use AudienceHero\Bundle\ContactBundle\Factory\ContactFactory;
use AudienceHero\Bundle\CoreBundle\Entity\User;
use Geocoder\Geocoder;
use Geocoder\Model\Address;
use Geocoder\Model\AddressCollection;
use Geocoder\Model\AdminLevelCollection;
use Geocoder\Model\Coordinates;
use Geocoder\Model\Country;
use Geocoder\Query\GeocodeQuery;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ContactFactoryTest extends \PHPUnit_Framework_TestCase
{
    /** @var RequestStack */
    private $requestStack;
    /** @var ObjectProphecy */
    private $geocoder;
    /** @var ObjectProphecy */
    private $serializer;
    /** @var ObjectProphecy */
    private $validator;
    /** @var User */
    private $owner;

    public function setUp()
    {
        $this->requestStack = new RequestStack();
        $this->geocoder = $this->prophesize(Geocoder::class);
        $this->serializer = $this->prophesize(SerializerInterface::class);
        $this->validator = $this->prophesize(ValidatorInterface::class);
        $this->owner = new User();
    }

    public function getInstance(): ContactFactory
    {
        return new ContactFactory($this->requestStack, $this->geocoder->reveal(), $this->serializer->reveal(), $this->validator->reveal());
    }

    public function testCreateGeolocatedDoesNotGeolocateIfRequestStackIsEmpty()
    {
        $this->geocoder->geocodeQuery(Argument::any())->shouldNotBeCalled();
        $contact = $this->getInstance()->createGeolocated($this->owner);
        $this->assertInstanceOf(Contact::class, $contact);
        $this->assertSame($this->owner, $contact->getOwner());
    }

    public function testCreateGeolocated()
    {
        $request = new Request([], [], [], [], [], ['REMOTE_ADDR' => '214.214.214.214']);
        $this->requestStack->push($request);

        $this->geocoder->geocodeQuery(Argument::that(function(GeocodeQuery $geocodeQuery) {
            return $geocodeQuery->getText() === '214.214.214.214';
        }))->shouldBeCalled()->willReturn(new AddressCollection(
            [
                new Address(
                    'test',
                    new AdminLevelCollection([]),
                    new Coordinates(45, 4),
                    null,
                    null,
                    null,
                    null,
                    'Lyon',
                    null,
                    new Country('France', 'FR')
                )
            ]
            )
        );

        $contact = $this->getInstance()->createGeolocated($this->owner);
        $this->assertInstanceOf(Contact::class, $contact);
        $this->assertSame($this->owner, $contact->getOwner());
        $this->assertSame('Lyon', $contact->getCity());
        $this->assertSame('FR', $contact->getCountry());
    }

    public function testCreateFromJson()
    {
        $contact = new Contact();

        $json = '{}';
        $this->serializer->deserialize($json, Contact::class, 'json', [
            'item_operation_name' => 'put',
            'operation_type' => 'item',
            'api_allow_update' => false,
            'resource_class' => Contact::class,
            'groups' => ['write'],
        ])->shouldBeCalled()->willReturn($contact);

        $this->validator->validate($contact, null, ['optin'])
            ->shouldBeCalled()
            ->willReturn(new ConstraintViolationList());

        $this->assertSame($contact, $this->getInstance()->createFromJson($json, $this->owner));
        $this->assertSame($this->owner, $contact->getOwner());
    }

    /**
     * @expectedException  \ApiPlatform\Core\Bridge\Symfony\Validator\Exception\ValidationException
     */
    public function testCreateFromJsonThrowsValidationException()
    {
        $contact = new Contact();

        $json = '{}';
        $this->serializer->deserialize($json, Contact::class, 'json', [
            'item_operation_name' => 'put',
            'operation_type' => 'item',
            'api_allow_update' => false,
            'resource_class' => Contact::class,
            'groups' => ['write'],
        ])->shouldBeCalled()->willReturn($contact);

        $this->validator->validate($contact, null, ['optin'])
            ->shouldBeCalled()
            ->willReturn(new ConstraintViolationList([$this->prophesize(ConstraintViolation::class)->reveal()]));

        $this->getInstance()->createFromJson($json, $this->owner);
    }
}
