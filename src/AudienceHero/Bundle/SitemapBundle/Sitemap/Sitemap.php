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

namespace AudienceHero\Bundle\SitemapBundle\Sitemap;

use AudienceHero\Bundle\SitemapBundle\Builder\BuilderCollection;
use AudienceHero\Bundle\SitemapBundle\Writer\WriterInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Thepixeldeveloper\Sitemap\Output;
use Thepixeldeveloper\Sitemap\Sitemap as SitemapLocation;
use Thepixeldeveloper\Sitemap\SitemapIndex;

/**
 * Sitemap.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
class Sitemap
{
    /** @var WriterInterface */
    private $writer;
    /** @var string */
    private $route;
    /** @var UrlGeneratorInterface */
    private $router;

    public function __construct(WriterInterface $writer, string $route, UrlGeneratorInterface $router)
    {
        $this->writer = $writer;
        $this->route = $route;
        $this->router = $router;
    }

    public function write(BuilderCollection $builders): void
    {
        $output = new Output();

        $index = new SitemapIndex();
        $base = $this->router->generate($this->route, [], UrlGeneratorInterface::ABSOLUTE_URL);
        foreach ($builders->getBuilders() as $name => $builder) {
            $urlset = $builder->build();

            $filename = $this->writer->write($name, $output->getOutput($urlset));
            $index->addSitemap(new SitemapLocation(sprintf('%s/%s', $base, $filename)));
        }

        $this->writer->write('', $output->getOutput($index));
    }
}
