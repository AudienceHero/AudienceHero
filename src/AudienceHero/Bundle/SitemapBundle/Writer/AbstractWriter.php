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
 * AbstractWriter.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
abstract class AbstractWriter implements WriterInterface
{
    protected function getFilename(string $key): string
    {
        if ($key) {
            $filename = sprintf('sitemap-%s.xml', $key);
        } else {
            $filename = 'sitemap.xml';
        }

        return $filename;
    }
}
