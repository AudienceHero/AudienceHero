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

namespace AudienceHero\Bundle\CoreBundle\Tests\Behavior\Ownable;

use AudienceHero\Bundle\CoreBundle\Behavior\Ownable\OwnableEntity;
use AudienceHero\Bundle\CoreBundle\Behavior\Ownable\OwnableInterface;
use AudienceHero\Bundle\CoreBundle\Entity\Person;
use PHPUnit\Framework\TestCase;

class OwnableEntityTest extends TestCase
{
    public function testOwnableEntity()
    {
        $object = new class() implements OwnableInterface
        {
            use OwnableEntity;
        };

        $person = $this->prophesize(Person::class)->reveal();

        $object->setOwner($person);
        $this->assertSame($person, $object->getOwner());
    }
}
