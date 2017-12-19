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

use AudienceHero\Bundle\FileBundle\Entity\File;
use AudienceHero\Bundle\FileBundle\ETL\Transformer\AudioTranscoderTransformer;
use AudienceHero\Bundle\FileBundle\Filesystem\FileSystemInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * AudioTranscodeStep.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
class AudioTranscodeStep implements StepInterface
{
    /** @var AudioTranscoderTransformer */
    private $transformer;
    /**
     * @var FileSystemInterface
     */
    private $filesystem;

    public function __construct(AudioTranscoderTransformer $transformer, FileSystemInterface $filesystem)
    {
        $this->transformer = $transformer;
        $this->filesystem = $filesystem;
    }

    public function run(Context $context): void
    {
        $file = $context->getFile();
        $transcoded = tempnam(sys_get_temp_dir(), 'tc');
        $this->transformer->transform($context->getPath(), $transcoded);

        $file128 = new File();
        $file128->setRemoteName(sprintf('%s-128.mp3', basename($file->getRemoteName(), '.'.$file->getExtension())));
        $uploadedFile = new UploadedFile($transcoded, $file128->getRemoteName());
        $this->filesystem->copy($uploadedFile, $file128);
        $file->setPublicMetadataValue('transcoded_128', $file128->getRemoteUrl());
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
