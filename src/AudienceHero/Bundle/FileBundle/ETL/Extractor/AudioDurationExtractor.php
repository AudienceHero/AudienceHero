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

namespace AudienceHero\Bundle\FileBundle\ETL\Extractor;

use Psr\Log\LoggerInterface;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\ProcessBuilder;

/**
 * AudioDurationExtractor.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
class AudioDurationExtractor
{
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function extract(string $in)
    {
        $pb = new ProcessBuilder(['soxi', '-D', $in]);

        try {
            $p = $pb->getProcess();
            $p->mustRun();

            return round($p->getOutput());
        } catch (ProcessFailedException $e) {
            $this->logger->error($e->getMessage());

            return null;
        }
    }
}
