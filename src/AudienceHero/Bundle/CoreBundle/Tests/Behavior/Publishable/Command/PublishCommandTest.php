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

namespace AudienceHero\Bundle\CoreBundle\Tests\Behavior\Publishable\Command;

use AudienceHero\Bundle\CoreBundle\Behavior\Publishable\Command\PublishCommand;
use AudienceHero\Bundle\CoreBundle\Behavior\Publishable\Publisher;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Tester\CommandTester;

class PublishCommandTest extends TestCase
{
    public function testCommand()
    {
        $manager = $this->prophesize(Publisher::class);
        $manager->publishScheduled()->shouldBeCalled();

        $command = new PublishCommand($manager->reveal());
        $tester = new CommandTester($command);
        $tester->execute([]);
    }
}
