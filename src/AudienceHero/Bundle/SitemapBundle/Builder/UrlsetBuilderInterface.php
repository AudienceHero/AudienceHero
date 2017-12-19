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

namespace AudienceHero\Bundle\SitemapBundle\Builder;

use Thepixeldeveloper\Sitemap\Urlset;

/**
 * BuilderInterface.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
interface UrlsetBuilderInterface
{
    public function getName(): string;

    public function build(): Urlset;
}
