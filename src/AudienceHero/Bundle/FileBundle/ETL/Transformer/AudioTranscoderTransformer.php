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

namespace AudienceHero\Bundle\FileBundle\ETL\Transformer;

use Psr\Log\LoggerInterface;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\ProcessBuilder;

/**
 * AudioTranscoderTransformer.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
class AudioTranscoderTransformer
{
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function transform($in, $out)
    {
        $pb = new ProcessBuilder(['lame', '--preset', '128', $in, $out]);

        try {
            $p = $pb->getProcess();
            $p->setTimeout(600);
            $p->mustRun();

            return true;
        } catch (ProcessFailedException $e) {
            $this->logger->error($e->getMessage());

            return false;
        }
    }
}
