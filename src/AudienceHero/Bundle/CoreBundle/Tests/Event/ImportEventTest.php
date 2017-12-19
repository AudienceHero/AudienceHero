<?php

/*
 * This file is part of the AudienceHero project.
 *
 * (c) Marc Weistroff <marc@weistroff.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AudienceHero\Bundle\CoreBundle\Tests\Event;

use AudienceHero\Bundle\CoreBundle\Entity\TextStore;
use AudienceHero\Bundle\CoreBundle\Event\ImportEvent;
use PHPUnit\Framework\TestCase;

class ImportEventTest extends TestCase
{
    public function testAccessors()
    {
        $ts = new TextStore();
        $event = new ImportEvent($ts);
        $this->assertSame($ts, $event->getTextStore());
    }
}
