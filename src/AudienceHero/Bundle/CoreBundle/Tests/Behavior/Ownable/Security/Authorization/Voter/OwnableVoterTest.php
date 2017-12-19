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

namespace AudienceHero\Bundle\CoreBundle\Tests\Behavior\Ownable\Security\Authorization\Voter;

use AudienceHero\Bundle\CoreBundle\Behavior\Ownable\OwnableInterface;
use AudienceHero\Bundle\CoreBundle\Entity\Person;
use AudienceHero\Bundle\CoreBundle\Entity\User;
use AudienceHero\Bundle\CoreBundle\Behavior\Ownable\Security\Authorization\Voter\OwnableVoter;
use AudienceHero\Bundle\PromoBundle\Entity\Promo;
use Symfony\Component\Security\Core\Authentication\Token\AnonymousToken;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class Ownable implements OwnableInterface
{
    public $owner;

    public function setOwner(Person $person)
    {
        $this->owner = $person;
    }

    public function getOwner()
    {
        return $this->owner;
    }
}

class OwnableVoterTest extends \PHPUnit_Framework_TestCase
{
    private $alice;
    private $bob;

    public function setUp()
    {
        $this->alice = new User();
        $this->alice->setId('alice');

        $this->bob = new User();
        $this->bob->setId('bob');

        $this->voter = new OwnableVoter();
        $this->token = $this->prophesize(TokenInterface::class);
    }

    public function testVoterDeniesIfUserIsAnonymous()
    {
        $this->assertEquals(VoterInterface::ACCESS_DENIED, $this->voter->vote(new AnonymousToken('foo', 'bar'), new Ownable(), ['IS_OWNER']));
    }

    /**
     * @expectedException \LogicException
     */
    public function testThrowsLogicExceptionIfOwnerIsNotAPerson()
    {
        $this->token->getUser()->willReturn(new Promo())->shouldBeCalled();

        $this->voter->vote($this->token->reveal(), new Ownable(), ['IS_OWNER']);
    }

    /**
     * @expectedException \LogicException
     */
    public function testThrowsLogicExceptionIfThereIsNoOwnerOnObject()
    {
        $this->token->getUser()->willReturn($this->alice)->shouldBeCalled();
        $this->voter->vote($this->token->reveal(), new Ownable(), ['IS_OWNER']);
    }

    public function testVoterGrantIfOwnerIsMatched()
    {
        $this->token->getUser()->willReturn($this->alice)->shouldBeCalled();

        $o = new Ownable();
        $o->setOwner($this->alice);
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $this->voter->vote($this->token->reveal(), $o, ['IS_OWNER']));
    }

    public function testVoterDeniesIfOwnerIsntMatched()
    {
        $this->token->getUser()->willReturn($this->alice)->shouldBeCalled();

        $o = new Ownable();
        $o->setOwner($this->bob);
        $this->assertEquals(VoterInterface::ACCESS_DENIED, $this->voter->vote($this->token->reveal(), $o, ['IS_OWNER']));
    }
}
