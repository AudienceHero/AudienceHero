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

namespace AudienceHero\Bundle\FileBundle\Queue;

use AudienceHero\Bundle\CoreBundle\Queue\Message;
use AudienceHero\Bundle\FileBundle\Entity\File;

/**
 * FileMessage.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
final class FileMessage extends Message
{
    /** @var File */
    private $file;

    /**
     * @return File
     */
    public function getFile(): ?File
    {
        return $this->file;
    }

    /**
     * @param File $file
     */
    public function setFile(File $file)
    {
        $this->file = $file;

        return $this;
    }
}
