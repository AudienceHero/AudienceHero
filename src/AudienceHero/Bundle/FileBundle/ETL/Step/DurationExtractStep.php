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

use AudienceHero\Bundle\FileBundle\ETL\Extractor\AudioDurationExtractor;

/**
 * DurationExtractorStep.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
class DurationExtractStep implements StepInterface
{
    private $extractor;

    public function __construct(AudioDurationExtractor $extractor)
    {
        $this->extractor = $extractor;
    }

    public function run(Context $context): void
    {
        $duration = $this->extractor->extract($context->getPath());
        $context->getFile()->setPublicMetadataValue('duration', $duration);
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
