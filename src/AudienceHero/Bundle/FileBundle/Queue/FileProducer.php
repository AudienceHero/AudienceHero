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

use AudienceHero\Bundle\CoreBundle\Queue\Producer;
use AudienceHero\Bundle\FileBundle\Entity\File;

/**
 * FileProducer.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
class FileProducer
{
    public const FILE_UPLOAD = 'audiencehero.file.upload';

    /** @var Producer */
    private $producer;

    public function __construct(Producer $producer)
    {
        $this->producer = $producer;
    }

    public function filesUpload(File $file)
    {
        $this->producer->sendCommand(self::FILE_UPLOAD, FileMessage::create()->setFile($file));
    }
}
