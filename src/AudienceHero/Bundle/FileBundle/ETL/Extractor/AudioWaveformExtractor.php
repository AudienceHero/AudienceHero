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
 * AudioWaveformExtractor.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
class AudioWaveformExtractor
{
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function extract(string $in): array
    {
        $pb = new ProcessBuilder(['waveform', $in]);

        try {
            $p = $pb->getProcess();
            $p->setTimeout(300);
            $p->mustRun();

            return json_decode($p->getOutput(), true);
        } catch (ProcessFailedException $e) {
            $this->logger->error($e->getMessage());

            return [];
        }
    }
}
