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

use AudienceHero\Bundle\SitemapBundle\Writer\MemoryWriter;
use PHPUnit\Framework\TestCase;

class MemoryWriterTest extends TestCase
{
    public function testWriter()
    {
        $writer = new MemoryWriter();
        $filename = $writer->write('foobar', 'my_content');
        $this->assertSame('sitemap-foobar.xml', $filename);

        $data = $writer->getData();
        $this->assertArrayHasKey($filename, $data);
        $this->assertSame('my_content', $data[$filename]);
    }
}
