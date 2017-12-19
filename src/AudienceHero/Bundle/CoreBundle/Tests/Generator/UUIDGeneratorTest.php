<?php

/*
 * This file is part of the AudienceHero project.
 *
 * (c) Marc Weistroff <marc@weistroff.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AudienceHero\Bundle\CoreBundle\Tests\Generator;

use AudienceHero\Bundle\CoreBundle\Generator\UUIDGenerator;
use PHPUnit\Framework\TestCase;

class UUIDGeneratorTest extends TestCase
{
    public function testGenerate()
    {
        $generator = new UUIDGenerator();
        $this->assertRegExp('/[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}/', $generator->generate());
    }
}
