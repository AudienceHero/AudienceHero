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

use AudienceHero\Bundle\CoreBundle\Security\TokenStorageAwareInterface;
use AudienceHero\Bundle\CoreBundle\Security\TokenStorageAwareTrait;
use AudienceHero\Bundle\FileBundle\Entity\File;
use AudienceHero\Bundle\FileBundle\MimeType\MP3MimeTypeGuesser;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\File\MimeType\MimeTypeGuesser;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileFactory implements TokenStorageAwareInterface
{
    use TokenStorageAwareTrait;

    public function createFromUploadedFile(UploadedFile $uploadedFile): File
    {
        $file = new File();

        $pi = pathinfo($uploadedFile->getClientOriginalName());

        $mimeType = $uploadedFile->getMimeType();
        // w00t, it might be an mp3 that is wrongly detected
        if ('application/octet-stream' === $mimeType) {
            $guesser = MimeTypeGuesser::getInstance();
            $guesser->register(new MP3MimeTypeGuesser());
            $mimeType = $guesser->guess($uploadedFile->getPathname());
        }

        $file->setContentType($mimeType);

        $file->setSize($uploadedFile->getSize());
        $file->setExtension($uploadedFile->guessExtension());
        if ('audio/mpeg' === $file->getContentType()) {
            $file->setExtension('mp3');
        }
        $file->setName($pi['filename']);
        $file->setRemoteName(sprintf('%s.%s', Uuid::uuid4(), $file->getExtension()));

        if ($file->isImage()) {
            $wh = getimagesize($uploadedFile->getPathname());
            $file->setPublicMetadataValue('width', $wh[0]);
            $file->setPublicMetadataValue('height', $wh[1]);
            $file->setPublicMetadataValue('ratio', round($wh[0] / $wh[1], 2, PHP_ROUND_HALF_ODD));
        }

        $file->setOwner($this->getUserFromTokenStorage());

        return $file;
    }
}
