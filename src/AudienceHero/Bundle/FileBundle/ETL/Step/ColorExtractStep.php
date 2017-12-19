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

use AudienceHero\Bundle\FileBundle\ETL\Extractor\ImageColorExtractor;

/**
 * ColorExtractor.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
class ColorExtractStep implements StepInterface
{
    private $extractor;

    public function __construct(ImageColorExtractor $extractor)
    {
        $this->extractor = $extractor;
    }

    public function run(Context $context): void
    {
        $file = $context->getFile();
        $colors = $this->extractor->extract($context->getPath());

        $file->setPublicMetadataValue('color.background', $colors['background']);
        $file->setPublicMetadataValue('color.primary', $colors['primary']);
        $file->setPublicMetadataValue('color.secondary', $colors['secondary']);
        $file->setPublicMetadataValue('color.detail', $colors['detail']);
    }

    public function supports(Context $context): bool
    {
        if (!$context->getFile()) {
            return false;
        }

        if (!$context->getPath()) {
            return false;
        }

        if (!$context->getFile()->isImage()) {
            return false;
        }

        return true;
    }

    public function getPriority(): int
    {
        return StepInterface::PRIORITY_NORMAL;
    }
}
