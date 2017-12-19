<?php

/*
 * This file is part of the AudienceHero project.
 *
 * (c) Marc Weistroff <marc@weistroff.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AudienceHero\Bundle\FileBundle\Tests\Event;

use AudienceHero\Bundle\FileBundle\Entity\File;
use AudienceHero\Bundle\FileBundle\Event\FileEvent;
use PHPUnit\Framework\TestCase;

class FileEventTest extends TestCase
{
    public function testAccessors()
    {
        $file = new File();
        $event = new FileEvent($file);
        $this->assertSame($file, $event->getFile());
    }
}
