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

namespace AudienceHero\Bundle\FileBundle\ETL\Step;

use AudienceHero\Bundle\FileBundle\ETL\Extractor\AudioWaveformExtractor;

/**
 * WaveformExtractor.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
class WaveformExtractStep implements StepInterface
{
    private $extractor;

    public function __construct(AudioWaveformExtractor $extractor)
    {
        $this->extractor = $extractor;
    }

    public function run(Context $context): void
    {
        $waveform = $this->extractor->extract($context->getPath());
        $context->getFile()->setPublicMetadataValue('waveform', $waveform);
    }

    public function supports(Context $context): bool
    {
        if (!$context->getFile()) {
            return false;
        }

        if (!$context->getPath()) {
            return false;
        }

        return $context->getFile()->isAudio();
    }

    public function getPriority(): int
    {
        return StepInterface::PRIORITY_NORMAL;
    }
}
