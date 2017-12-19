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

use AppBundle\Swarrot\Producer;
use AudienceHero\Bundle\FileBundle\Queue\FileProducer;
use AudienceHero\Bundle\FileBundle\Repository\FileRepository;
use Psr\Log\LoggerInterface;
use Swarrot\Broker\Message;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * ProcessCommand.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
class ProcessCommand extends Command
{
    /** @var FileRepository */
    private $repository;
    /** @var Producer */
    private $producer;
    /** @var LoggerInterface */
    private $logger;

    public function __construct(FileRepository $fileRepository, FileProducer $producer, LoggerInterface $logger)
    {
        $this->repository = $fileRepository;
        $this->producer = $producer;
        $this->logger = $logger;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('audiencehero:file:process')
            ->setDescription('Send files with missing data for processing')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var \AudienceHero\Bundle\FileBundle\Entity\File[] $files */
        $files = $this->repository->findProcessable();
        foreach ($files as $file) {
            $this->logger->info(sprintf('Sending message for file %s', $file->getId()));
            $this->producer->filesUpload($file);
        }
    }
}
