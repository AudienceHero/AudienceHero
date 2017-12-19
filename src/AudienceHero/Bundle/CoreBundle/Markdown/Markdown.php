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

namespace AudienceHero\Bundle\CoreBundle\Markdown;

use cebe\markdown\block;
use cebe\markdown\inline;
use cebe\markdown\Parser;

/**
 * Markdown.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
class Markdown extends Parser
{
    use block\HeadlineTrait;
    use block\ListTrait;

    use inline\EmphStrongTrait;
    use inline\LinkTrait;
    use inline\UrlLinkTrait;

    public $html5 = false;
    public $enableNewlines = true;

    protected function prepare()
    {
        $this->references = [];
    }

    protected function renderText($text)
    {
        if ($this->enableNewlines) {
            $br = $this->html5 ? "<br>\n" : "<br />\n";

            return strtr($text[1], ["  \n" => $br, "\n" => $br]);
        }

        return parent::renderText($text);
    }
}
