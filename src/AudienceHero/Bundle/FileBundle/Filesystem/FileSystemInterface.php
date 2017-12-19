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
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * FilesystemInterface.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
interface FileSystemInterface
{
    public function copy(UploadedFile $uploadedFile, File $file): void;

    public function resolveUrl(File $file): string;

    public function remove(File $file): void;
}
