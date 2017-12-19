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

namespace AudienceHero\Bundle\CoreBundle\Tests\Behavior\Linkable;

use AudienceHero\Bundle\CoreBundle\Behavior\Linkable\LinkableEntity;
use AudienceHero\Bundle\CoreBundle\Behavior\Linkable\LinkableInterface;
use PHPUnit\Framework\TestCase;

class LinkableEntityTest extends TestCase
{
    public function testTrait()
    {
        $object = new class() implements LinkableInterface {
            use LinkableEntity;
        };

        $this->assertInternalType('array', $object->getURLs());
        $this->assertEmpty($object->getURLs());
        $this->assertNull($object->getURL('foobar'));

        $object->setURL('foobar', 'https://foo.bar');
        $this->assertSame('https://foo.bar', $object->getURL('foobar'));
        $this->assertSame(['foobar' => 'https://foo.bar'], $object->getURLs());

        $urls = ['foo-bar' => 'https://dum.my'];
        $object->setURLs($urls);
        $this->assertSame($urls, $object->getURLs());
        $this->assertSame('https://dum.my', $object->getURL('foo-bar'));
        $this->assertNull($object->getURL('foobar'));
    }
}
