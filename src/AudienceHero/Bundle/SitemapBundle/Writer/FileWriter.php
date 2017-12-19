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

namespace AudienceHero\Bundle\SitemapBundle\Writer;

/**
 * FileWriter.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
class FileWriter extends AbstractWriter
{
    /** @var string */
    private $dir;

    /**
     * FileWriter constructor.
     *
     * @param string $dir Directory where content will be written
     */
    public function __construct(string $dir)
    {
        $this->dir = $dir;
    }

    /**
     * @param $key will be used to create the filename
     * @param $content the content of the sitemap
     *
     * @return string the name of the created file
     */
    public function write($key, $content): string
    {
        $filename = $this->getFilename($key);
        $path = sprintf('%s/%s', rtrim($this->dir, '/'), $filename);
        file_put_contents($path, $content);

        return $filename;
    }
}
