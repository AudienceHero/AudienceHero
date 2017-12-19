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

namespace AudienceHero\Bundle\ActivityBundle\Tests\Command;

use AudienceHero\Bundle\ActivityBundle\Command\EnrichCommand;
use AudienceHero\Bundle\ActivityBundle\Enricher\ChainEnricher;
use AudienceHero\Bundle\ActivityBundle\Entity\Activity;
use AudienceHero\Bundle\ActivityBundle\Repository\ActivityRepository;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Tester\CommandTester;

class EnrichCommandTest extends TestCase
{
    public function testCommand()
    {
        $repository = $this->prophesize(ActivityRepository::class);
        $enricher = $this->prophesize(ChainEnricher::class);

        $a1 = new Activity();
        $a2 = new Activity();

        $repository->findAll()->willReturn([$a1, $a2])->shouldBeCalled();

        $enricher->enrich($a1)->shouldBeCalled();
        $enricher->enrich($a2)->shouldBeCalled();
        $repository->flush()->shouldBeCalled();

        $command = new EnrichCommand($repository->reveal(), $enricher->reveal());
        $tester = new CommandTester($command);
        $tester->execute([]);
    }
}
