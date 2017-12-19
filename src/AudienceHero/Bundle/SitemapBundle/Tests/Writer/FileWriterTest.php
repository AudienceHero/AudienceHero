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

namespace AudienceHero\Bundle\SitemapBundle\Tests\Writer;

use AudienceHero\Bundle\SitemapBundle\Writer\FileWriter;
use PHPUnit\Framework\TestCase;

class FileWriterTest extends TestCase
{
    public function testWrite()
    {
        $writer = new FileWriter(__DIR__);
        $filename = $writer->write('foobar', 'my_content');
        $path = __DIR__.'/'.$filename;

        $this->assertSame('sitemap-foobar.xml', $filename);
        $this->assertFileExists($path);
        $this->assertSame('my_content', file_get_contents($path));
        @unlink($filename);
    }
}
