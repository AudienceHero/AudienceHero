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

class ImageColorExtractor
{
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function extract(string $in): array
    {
        $pb = new ProcessBuilder(['colorart', '-resize-size=250', '-contrast=1.6', '-blur=false', $in]);

        try {
            $p = $pb->getProcess();
            $p->setTimeout(10);
            $p->mustRun();

            return json_decode($p->getOutput(), true);
        } catch (ProcessFailedException $e) {
            $this->logger->error($e->getMessage());

            return [];
        }
    }
}
