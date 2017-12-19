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

namespace AudienceHero\Bundle\FileBundle\Command;

use AudienceHero\Bundle\FileBundle\Entity\File;
use AudienceHero\Bundle\FileBundle\Queue\FileProducer;
use AudienceHero\Bundle\FileBundle\Repository\FileRepository;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Tester\CommandTester;

class ProcessCommandTest extends TestCase
{
    public function testCommand()
    {
        $repository = $this->prophesize(FileRepository::class);
        $logger = $this->prophesize(LoggerInterface::class);
        $producer = $this->prophesize(FileProducer::class);

        $file1 = $this->prophesize(File::class);
        $file1->getId()->willReturn('id1')->shouldBeCalled();
        $file2 = $this->prophesize(File::class);
        $file2->getId()->willReturn('id2')->shouldBeCalled();

        $repository->findProcessable()->willReturn([$file1->reveal(), $file2->reveal()])->shouldBeCalled();
        $logger->info('Sending message for file id1')->shouldBeCalled();
        $logger->info('Sending message for file id2')->shouldBeCalled();

        $producer->filesUpload($file1)->shouldBeCalled();
        $producer->filesUpload($file2)->shouldBeCalled();

        $command = new ProcessCommand($repository->reveal(), $producer->reveal(), $logger->reveal());
        $tester = new CommandTester($command);
        $tester->execute([]);
    }
}
