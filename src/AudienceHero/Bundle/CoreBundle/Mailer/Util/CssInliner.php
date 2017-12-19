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

namespace AudienceHero\Bundle\CoreBundle\Mailer\Util;

use TijsVerkoyen\CssToInlineStyles\CssToInlineStyles;

/**
 * CssInliner.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
final class CssInliner
{
    public function inline($html)
    {
        $inliner = new CssToInlineStyles();

        return $inliner->convert($html);
    }
}
