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

/**
 * Context.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
class Context
{
    /** @var File */
    private $file;

    /** @var string */
    private $path;

    public function __construct(File $file)
    {
        $this->file = $file;
    }

    /**
     * @return string
     */
    public function getPath(): ?string
    {
        return $this->path;
    }

    /**
     * @param string $path
     */
    public function setPath(string $path)
    {
        $this->path = $path;
    }

    /**
     * @return File
     */
    public function getFile(): File
    {
        return $this->file;
    }
}
