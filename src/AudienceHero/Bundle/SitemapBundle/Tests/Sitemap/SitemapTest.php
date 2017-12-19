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

namespace AudienceHero\Bundle\SitemapBundle\Tests\Sitemap;

use AudienceHero\Bundle\SitemapBundle\Builder\BuilderCollection;
use AudienceHero\Bundle\SitemapBundle\Builder\UrlsetBuilderInterface;
use AudienceHero\Bundle\SitemapBundle\Sitemap\Sitemap;
use AudienceHero\Bundle\SitemapBundle\Writer\MemoryWriter;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Thepixeldeveloper\Sitemap\Url;
use Thepixeldeveloper\Sitemap\Urlset;

class SitemapTest extends TestCase
{
    /** @var MemoryWriter */
    private $writer;
    /** @var ObjectProphecy */
    private $router;
    private $route = 'homepage';

    public function setUp()
    {
        $this->writer = new MemoryWriter();
        $this->router = $this->prophesize(UrlGeneratorInterface::class);
    }

    public function testSitemap()
    {
        $builder1 = $this->prophesize(UrlsetBuilderInterface::class);
        $builder1->getName()->willReturn('builder1')->shouldBeCalled();
        $builder1->build()->willReturn(new Urlset())->shouldBeCalledTimes(1);

        $urlset = new Urlset();
        $urlset->addUrl(new Url('http://www.example.com/foobar'));
        $builder2 = $this->prophesize(UrlsetBuilderInterface::class);
        $builder2->getName()->willReturn('builder2')->shouldBeCalled();
        $builder2->build()->willReturn($urlset)->shouldBeCalledTimes(1);

        $url = 'http://www.example.com';
        $this->router->generate($this->route, [], UrlGeneratorInterface::ABSOLUTE_URL)
                     ->willReturn($url)
                     ->shouldBeCalledTimes(1);

        $sitemap = new Sitemap($this->writer, $this->route, $this->router->reveal());
        $collection = new BuilderCollection();
        $collection->addBuilder($builder1->reveal());
        $collection->addBuilder($builder2->reveal());
        $sitemap->write($collection);

        $sitemaps = $this->writer->getData();
        $this->assertArrayHasKey('sitemap.xml', $sitemaps);
        $this->assertArrayHasKey('sitemap-builder1.xml', $sitemaps);
        $this->assertArrayHasKey('sitemap-builder2.xml', $sitemaps);

        $expectedSitemap = <<<EOF
<?xml version="1.0" encoding="UTF-8"?>
<sitemapindex xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/siteindex.xsd" xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <sitemap>
        <loc>http://www.example.com/sitemap-builder1.xml</loc>
    </sitemap>
    <sitemap>
        <loc>http://www.example.com/sitemap-builder2.xml</loc>
    </sitemap>
</sitemapindex>
EOF;

        $expectedBuilder1 = <<<EOF
<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/siteindex.xsd" xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"/>
EOF;

        $expectedBuilder2 = <<<EOF
<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/siteindex.xsd" xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <url>
        <loc>http://www.example.com/foobar</loc>
    </url>
</urlset>
EOF;

        $this->assertSame($expectedSitemap, $sitemaps['sitemap.xml']);
        $this->assertSame($expectedBuilder1, $sitemaps['sitemap-builder1.xml']);
        $this->assertSame($expectedBuilder2, $sitemaps['sitemap-builder2.xml']);
    }
}
