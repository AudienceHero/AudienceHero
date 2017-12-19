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

use AudienceHero\Bundle\ActivityBundle\Aggregator\ChainAggregator;
use AudienceHero\Bundle\ActivityBundle\CollectionBuilder\ChainEntityCollectionBuilder;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AggregateCommand extends Command
{
    /**
     * @var ChainEntityCollectionBuilder
     */
    private $collectionBuilder;
    /**
     * @var ChainAggregator
     */
    private $chainAggregator;
    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(ChainEntityCollectionBuilder $collectionBuilder, ChainAggregator $chainAggregator, LoggerInterface $logger)
    {
        $this->collectionBuilder = $collectionBuilder;
        $this->chainAggregator = $chainAggregator;
        $this->logger = $logger;

        parent::__construct(null);
    }

    protected function configure()
    {
        $this
            ->setName('audiencehero:activity:aggregate')
            ->setDescription('Compute aggregate of Activities.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $entities = $this->collectionBuilder->build();

        foreach ($entities as $id => $types) {
            foreach ($types as $type) {
                try {
                    $this->logger->debug(sprintf('Computing aggregates for %s:%s', $type, $id));
                    $this->chainAggregator->compute($id, $type);
                } catch (\Exception $exception) {
                    $this->logger->error('Error while computing aggregate aggregate.', ['id' => $id, 'type' => $type, 'exception' => $exception]);
                }
            }
        }
    }
}
