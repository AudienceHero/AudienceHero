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

namespace AudienceHero\Bundle\CoreBundle\Tests\Behavior\Identifiable;

use AudienceHero\Bundle\CoreBundle\Behavior\Identifiable\IdentifiableEntity;
use AudienceHero\Bundle\CoreBundle\Behavior\Identifiable\IdentifiableInterface;
use PHPUnit\Framework\TestCase;

class IdentifiableDummy implements IdentifiableInterface
{
    use IdentifiableEntity;

    public function setId($foo)
    {
        $this->id = $foo;
    }
}

class IdentifiableEntityTest extends TestCase
{
    public function testTrait()
    {
        $instance = new IdentifiableDummy();
        $this->assertNull($instance->getId());
        $instance->setId('foo');
        $this->assertSame('foo', $instance->getId());
        $this->assertSame('identifiable_dummies', $instance->getSoftReferenceKey());
    }
}
