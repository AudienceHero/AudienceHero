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

namespace AudienceHero\Bundle\CoreBundle\Tests\Entity;

use AudienceHero\Bundle\CoreBundle\Entity\TextStore;
use PHPUnit\Framework\TestCase;

class TextStoreTest extends TestCase
{
    public function testSetTextCorrectsEncoding()
    {
        $ts = new TextStore();
        $string = 'my string éść';

        $this->assertSame('UTF-8', mb_detect_encoding($string, mb_detect_order(), true));
        $ts->setText($string);
        $this->assertSame($string, $ts->getText());
        $this->assertSame('UTF-8', mb_detect_encoding($ts->getText(), mb_detect_order(), true));

        $converted = mb_convert_encoding($string, 'ISO-8859-1');
        $ts->setText($converted);
        $this->assertSame('my string é??', $ts->getText());
        $this->assertSame('UTF-8', mb_detect_encoding($ts->getText(), mb_detect_order(), true));

        $converted = mb_convert_encoding($string, 'EUC-JP');
        $ts->setText($converted);
        $this->assertSame('my string «±«Ü««', $ts->getText());
        $this->assertSame('UTF-8', mb_detect_encoding($ts->getText(), mb_detect_order(), true));
    }
}
