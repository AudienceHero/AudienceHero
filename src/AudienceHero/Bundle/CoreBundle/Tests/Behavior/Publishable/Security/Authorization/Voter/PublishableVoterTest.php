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

namespace AudienceHero\Bundle\CoreBundle\Tests\Security\Authorization\Voter;

use AudienceHero\Bundle\CoreBundle\Behavior\Ownable\OwnableEntity;
use AudienceHero\Bundle\CoreBundle\Behavior\Ownable\OwnableInterface;
use AudienceHero\Bundle\CoreBundle\Behavior\Publishable\PublishableEntity;
use AudienceHero\Bundle\CoreBundle\Behavior\Publishable\PublishableInterface;
use AudienceHero\Bundle\CoreBundle\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\AnonymousToken;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

final class Publishable implements OwnableInterface, PublishableInterface
{
    use PublishableEntity;
    use OwnableEntity;
}

class PublishableVoterTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->voter = new \AudienceHero\Bundle\CoreBundle\Behavior\Publishable\Security\Authorization\Voter\PublishableVoter();
    }

    public function getAccessMatrix()
    {
        $alice = new User();
        $alice->setId('alice');

        $bob = new User();
        $bob->setId('bob');

        $publicObject = new Publishable();
        $publicObject->setPrivacy(PublishableInterface::PRIVACY_PUBLIC);

        $unlistedObject = new Publishable();
        $unlistedObject->setPrivacy(PublishableInterface::PRIVACY_UNLISTED);

        $scheduledObjectOwnedByAlice = new Publishable();
        $scheduledObjectOwnedByAlice->setPrivacy(PublishableInterface::PRIVACY_SCHEDULED);
        $scheduledObjectOwnedByAlice->setOwner($alice);

        $privateObjectOwnedByAlice = new Publishable();
        $privateObjectOwnedByAlice->setPrivacy(PublishableInterface::PRIVACY_PRIVATE);
        $privateObjectOwnedByAlice->setOwner($alice);

        $privateObjectOwnedByBob = new Publishable();
        $privateObjectOwnedByBob->setPrivacy(PublishableInterface::PRIVACY_PRIVATE);
        $privateObjectOwnedByBob->setOwner($bob);

        return [
            0 => [$publicObject, 'anon', VoterInterface::ACCESS_GRANTED],
            1 => [$publicObject, $alice, VoterInterface::ACCESS_GRANTED],
            2 => [$publicObject, $bob, VoterInterface::ACCESS_GRANTED],
            3 => [$unlistedObject, 'anon', VoterInterface::ACCESS_GRANTED],
            4 => [$unlistedObject, $alice, VoterInterface::ACCESS_GRANTED],
            5 => [$unlistedObject, $bob, VoterInterface::ACCESS_GRANTED],
            6 => [$scheduledObjectOwnedByAlice, 'anon', VoterInterface::ACCESS_DENIED],
            7 => [$scheduledObjectOwnedByAlice, $alice, VoterInterface::ACCESS_GRANTED],
            8 => [$scheduledObjectOwnedByAlice, $bob, VoterInterface::ACCESS_DENIED],
            9 => [$privateObjectOwnedByAlice, 'anon', VoterInterface::ACCESS_DENIED],
            10 => [$privateObjectOwnedByAlice, $alice, VoterInterface::ACCESS_GRANTED],
            11 => [$privateObjectOwnedByAlice, $bob, VoterInterface::ACCESS_DENIED],
            12 => [$privateObjectOwnedByBob, 'anon', VoterInterface::ACCESS_DENIED],
            13 => [$privateObjectOwnedByBob, $alice, VoterInterface::ACCESS_DENIED],
            14 => [$privateObjectOwnedByBob, $bob, VoterInterface::ACCESS_GRANTED],
        ];
    }

    /**
     * @dataProvider getAccessMatrix
     */
    public function testVoter(PublishableInterface $object, $user, $result)
    {
        if (is_object($user)) {
            $mock = $this->prophesize(TokenInterface::class);
            $mock->getUser()->willReturn($user);
            $token = $mock->reveal();
        } else {
            $token = new AnonymousToken('foo', 'bar');
        }

        $this->assertEquals($result, $this->voter->vote($token, $object, ['FRONT_SEE']));
    }
}
