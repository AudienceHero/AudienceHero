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

use AudienceHero\Bundle\FileBundle\Entity\File;
use Aws\S3\S3Client;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * S3FileSystem.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
class S3FileSystem implements FileSystemInterface
{
    /**
     * @var S3Client
     */
    private $client;

    /**
     * @var string
     */
    private $bucket;
    /**
     * @var string
     */
    private $uploadDir;

    public function __construct(S3Client $client, string $bucket, string $uploadDir = 'upload')
    {
        $this->client = $client;
        $this->bucket = $bucket;
        $this->uploadDir = trim($uploadDir, '/');
    }

    public function copy(UploadedFile $uploadedFile, File $file): void
    {
        $this->client->registerStreamWrapper();

        $fs = new Filesystem();
        $key = $file->getRemoteName();

        $s3Filename = sprintf('s3://%s/%s/%s', $this->bucket, $this->uploadDir, $key);
        $result = @copy($uploadedFile->getPathname(), $s3Filename, stream_context_create(['s3' => ['ACL' => 'public-read']]));

        if (!$result) {
            throw new \RuntimeException('Impossible to copy file to S3');
        }

        $result = @unlink($uploadedFile->getPathname());
        if (!$result) {
            throw new \RuntimeException('Impossible to unlink uploaded file');
        }

        $uri = sprintf('https://s3-%s.amazonaws.com/%s/%s/%s', $this->client->getRegion(), $this->bucket, $this->uploadDir, $key);

        $file->setUri($uri);
    }

    public function resolveUrl(File $file): string
    {
        $filename = @iconv('UTF-8', 'ASCII//TRANSLIT', $file->__toString());
        if (false === $filename) {
            $filename = sprintf('file.%s', $file->getExtension());
        }

        $cmd = $this->client->getCommand('GetObject', [
            'Bucket' => $this->bucket,
            'Key' => sprintf('%s/%s', $this->uploadDir, $file->getRemoteName()),
            'ResponseContentType' => $file->getContentType(),
            'ResponseContentDisposition' => sprintf('attachement; filename="%s"', $filename),
        ]);

        $s3Request = $this->client->createPresignedRequest($cmd, '+20 minutes');

        return $s3Request->getUri();
    }

    public function remove(File $file): void
    {
        $this->client->deleteObject([
            'Bucket' => $this->bucket,
            'Key' => sprintf('%s/%s', $this->uploadDir, $file->getRemoteName()),
        ]);
    }
}
