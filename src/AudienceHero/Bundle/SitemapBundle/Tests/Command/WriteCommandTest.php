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

namespace AudienceHero\Bundle\SitemapBundle\Tests\Command;

use AudienceHero\Bundle\SitemapBundle\Builder\BuilderCollection;
use AudienceHero\Bundle\SitemapBundle\Command\WriteCommand;
use AudienceHero\Bundle\SitemapBundle\Sitemap\Sitemap;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Tester\CommandTester;

class WriteCommandTest extends TestCase
{
    public function testCommand()
    {
        $prophecy = $this->prophesize(BuilderCollection::class);
        $collection = $prophecy->reveal();
        $sitemap = $this->prophesize(Sitemap::class);

        $sitemap->write($collection)->shouldBeCalled();

        $command = new WriteCommand($sitemap->reveal(), $collection);
        $tester = new CommandTester($command);
        $tester->execute([]);
    }
}
