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
 * MemoryWriter.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
class MemoryWriter extends AbstractWriter
{
    /** @var array */
    private $data = [];

    /**
     * @param $key will be used to create the filename
     * @param $content the content of the sitemap
     *
     * @return string the name of the created file
     */
    public function write($key, $content): string
    {
        $filename = $this->getFilename($key);
        $this->data[$filename] = $content;

        return $filename;
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }
}
