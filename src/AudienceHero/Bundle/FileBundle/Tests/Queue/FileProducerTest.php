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

use AudienceHero\Bundle\CoreBundle\Queue\Producer;
use AudienceHero\Bundle\FileBundle\Entity\File;
use AudienceHero\Bundle\FileBundle\Queue\FileMessage;
use AudienceHero\Bundle\FileBundle\Queue\FileProducer;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;

class FileProducerTest extends TestCase
{
    private $coreProducer;
    private $producer;

    public function setUp()
    {
        $this->coreProducer = $this->prophesize(Producer::class);
        $this->producer = new FileProducer($this->coreProducer->reveal());
    }

    public function testFilesUpload()
    {
        $file = new File();
        $this->coreProducer->sendCommand(
           FileProducer::FILE_UPLOAD,
            Argument::that(function(FileMessage $message) use ($file) {
                return $message->getFile() === $file;
            })
        )->shouldBeCalled();

        $this->producer->filesUpload($file);
    }
}
