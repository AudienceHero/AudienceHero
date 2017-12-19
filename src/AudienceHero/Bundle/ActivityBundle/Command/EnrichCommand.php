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

namespace AudienceHero\Bundle\ActivityBundle\Command;

use AudienceHero\Bundle\ActivityBundle\Enricher\ChainEnricher;
use AudienceHero\Bundle\ActivityBundle\Entity\Activity;
use AudienceHero\Bundle\ActivityBundle\Repository\ActivityRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * EnrichCommand.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
class EnrichCommand extends Command
{
    /** @var ActivityRepository */
    private $repository;
    /** @var ChainEnricher */
    private $enricher;

    public function __construct(ActivityRepository $repository, ChainEnricher $enricher)
    {
        $this->repository = $repository;
        $this->enricher = $enricher;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('audiencehero:activity:enrich')
            ->setDescription('Process all activities for enrichment')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $activities = $this->repository->findAll();
        foreach ($activities as $activity) {
            $this->enricher->enrich($activity);
        }

        $this->repository->flush();
    }
}
