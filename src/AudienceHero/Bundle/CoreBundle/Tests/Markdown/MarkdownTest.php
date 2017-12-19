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

namespace AudienceHero\Bundle\CoreBundle\Tests\Markdown;

class MarkdownTest extends \PHPUnit_Framework_TestCase
{
    public function testRendering()
    {
        $md = <<<EOF
# This is my heading

This is my second heading
-------------------------

**yabadabadou** _foobar_ [yabada](http://foo.bar)

http://audiencehero.org

<script>window.alert('foobar');</script>
EOF;

        $expected = <<<EOF
<h1>This is my heading</h1>
<h2>This is my second heading</h2>
<p><strong>yabadabadou</strong> <em>foobar</em> <a href="http://foo.bar">yabada</a></p>
<p><a href="http://audiencehero.org">http://audiencehero.org</a></p>
<p>&lt;script>window.alert('foobar');&lt;/script></p>
EOF;

        $parser = new \AudienceHero\Bundle\CoreBundle\Markdown\Markdown();
        $this->assertEquals($expected, trim($parser->parse($md)));
    }
}
