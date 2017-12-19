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

namespace AudienceHero\Bundle\SitemapBundle\Command;

use AudienceHero\Bundle\SitemapBundle\Builder\BuilderCollection;
use AudienceHero\Bundle\SitemapBundle\Sitemap\Sitemap;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class WriteCommand extends Command
{
    /** @var Sitemap */
    private $sitemap;
    /** @var BuilderCollection */
    private $builderCollection;

    public function __construct(Sitemap $sitemap, BuilderCollection $builderCollection)
    {
        $this->sitemap = $sitemap;
        $this->builderCollection = $builderCollection;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('audiencehero:sitemap:write')
            ->setDescription('Generate sitemaps files and ping search engines about changes');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->sitemap->write($this->builderCollection);
    }
}
