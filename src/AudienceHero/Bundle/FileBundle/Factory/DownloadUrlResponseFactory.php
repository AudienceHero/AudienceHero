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

namespace AudienceHero\Bundle\FileBundle\Factory;

use AudienceHero\Bundle\FileBundle\Entity\File;
use AudienceHero\Bundle\FileBundle\Model\DownloadUrlResponse;
use AudienceHero\Bundle\AcquisitionFreeDownloadBundle\Entity\AcquisitionFreeDownload;
use AudienceHero\Bundle\FileBundle\Filesystem\FileSystemInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * DownloadUrlResponseFactory
 * @author Marc Weistroff <marc@weistroff.net>
 */
class DownloadUrlResponseFactory
{
    /**
     * @var FileSystemInterface
     */
    private $fileSystem;
    /**
     * @var SerializerInterface
     */
    private $serializer;

    public function __construct(FileSystemInterface $fileSystem, SerializerInterface $serializer)
    {
        $this->fileSystem = $fileSystem;
        $this->serializer = $serializer;
    }

    public function create(File $file): Response
    {
        $response = new DownloadUrlResponse();
        $response->setUrl($this->fileSystem->resolveUrl($file));

        return new Response(
            $this->serializer->serialize($response, 'json'),
            200,
            ['Content-Type' => 'application/ld+json']
        );
    }
}