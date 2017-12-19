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

namespace AudienceHero\Bundle\FileBundle\Filesystem;

use ApiPlatform\Core\Api\UrlGeneratorInterface;
use AudienceHero\Bundle\FileBundle\Entity\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @author Marc Weistroff <marc@weistroff.net>
 */
class LocalFileSystem implements FileSystemInterface
{
    /**
     * @var string
     */
    private $uploadDir;
    /**
     * @var string
     */
    private $basePath;
    /**
     * @var UrlGeneratorInterface
     */
    private $generator;

    public function __construct(string $basePath, string $uploadDir, UrlGeneratorInterface $generator)
    {
        $this->uploadDir = trim($uploadDir, '/');
        $this->basePath = $basePath;
        $this->generator = $generator;
    }

    public function copy(UploadedFile $uploadedFile, File $file): void
    {
        $result = @copy($uploadedFile->getPathname(), sprintf('%s/%s/%s', $this->basePath, $this->uploadDir, $file->getRemoteName()));

        if (!$result) {
            throw new \RuntimeException('Impossible to copy file');
        }

        $result = @unlink($uploadedFile->getPathname());
        if (!$result) {
            throw new \RuntimeException('Impossible to unlink uploaded file');
        }

        $file->setRemoteUrl($this->resolveUrl($file));
    }

    public function resolveUrl(File $file): string
    {
        return sprintf('%s/%s/%s',
            rtrim($this->generator->generate('homepage', [], UrlGeneratorInterface::ABS_URL), '/'),
            $this->uploadDir,
            $file->getRemoteName()
        );
    }

    public function remove(File $file): void
    {
        @unlink(sprintf('%s/%s/%s', $this->basePath, $this->uploadDir, $file->getRemoteName()));
    }
}
