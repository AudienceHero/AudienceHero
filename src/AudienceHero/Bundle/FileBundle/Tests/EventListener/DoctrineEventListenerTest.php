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

namespace AudienceHero\Bundle\FileBundle\Tests\EventListener;

use AudienceHero\Bundle\FileBundle\Entity\File;
use AudienceHero\Bundle\FileBundle\EventListener\DoctrineEventListener;
use AudienceHero\Bundle\FileBundle\Filesystem\FileSystemInterface;
use AudienceHero\Bundle\FileBundle\Queue\FileProducer;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;

class DoctrineEventListenerTest extends TestCase
{
    /** @var ObjectProphecy */
    private $producer;
    /** @var ObjectProphecy */
    private $filesystem;

    public function setUp()
    {
        $this->producer = $this->prophesize(FileProducer::class);
        $this->manager = $this->prophesize(EntityManagerInterface::class);
        $this->filesystem = $this->prophesize(FileSystemInterface::class);
    }

    public function testPostPersistDoesNotHandleNonFileInstances()
    {
        $this->producer->filesUpload()->shouldNotBeCalled();
        $listener = new DoctrineEventListener($this->filesystem->reveal(), $this->producer->reveal());

        $event = new LifecycleEventArgs(new \stdClass(), $this->manager->reveal());
        $listener->postPersist($event);
    }

    public function testPostPersistHandleFileInstances()
    {
        $file = new File();

        $this->filesystem->resolveUrl($file)->shouldBeCalled()->willReturn('http://foo');
        $this->producer->filesUpload($file)->shouldBeCalled();

        $listener = new DoctrineEventListener($this->filesystem->reveal(), $this->producer->reveal());
        $event = new LifecycleEventArgs($file, $this->manager->reveal());
        $listener->postPersist($event);
    }
}
