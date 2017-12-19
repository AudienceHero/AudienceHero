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

namespace AudienceHero\Bundle\CoreBundle\Test\Security;

use AudienceHero\Bundle\CoreBundle\Entity\Person;
use AudienceHero\Bundle\CoreBundle\Security\TokenStorageAwareInterface;
use AudienceHero\Bundle\CoreBundle\Security\TokenStorageAwareTrait;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class TokenStorageAwareTraitTest extends TestCase
{
    /** @var ObjectProphecy */
    private $object;
    /** @var ObjectProphecy */
    private $storage;

    public function setUp()
    {
        $this->object = new class() implements TokenStorageAwareInterface {
            use TokenStorageAwareTrait;
        };

        $this->storage = $this->prophesize(TokenStorageInterface::class);
    }

    public function testGetUserReturnsNull()
    {
        $this->storage->getToken()->willReturn(null)->shouldBeCalled();

        $this->object->setTokenStorage($this->storage->reveal());
        $this->assertNull($this->object->getUserFromTokenStorage());
    }

    public function testGetUserReturnsPerson()
    {
        $person = $this->prophesize(Person::class)->reveal();
        $token = new UsernamePasswordToken($person, '', 'foobar', ['ROLE_USER']);

        $this->storage->getToken()->willReturn($token)->shouldBeCalled();
        $this->object->setTokenStorage($this->storage->reveal());
        $this->assertSame($person, $this->object->getUserFromTokenStorage());
    }
}
