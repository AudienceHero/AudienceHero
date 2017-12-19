<?php

/*
 * This file is part of the AudienceHero project.
 *
 * (c) Marc Weistroff <marc@weistroff.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AudienceHero\Bundle\FileBundle\Tests\Queue;

use AudienceHero\Bundle\FileBundle\Entity\File;
use AudienceHero\Bundle\FileBundle\Queue\FileMessage;
use PHPUnit\Framework\TestCase;

class FileMessageTest extends TestCase
{
    public function testAccessors()
    {
        /** @var FileMessage $message */
        $message = FileMessage::create();
        $this->assertInstanceOf(FileMessage::class, $message);

        $file = new File();
        $this->assertNull($message->getFile());
        $this->assertSame($message, $message->setFile($file));
        $this->assertSame($file, $message->getFile());
    }
}
