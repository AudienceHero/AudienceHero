<?php

/*
 * This file is part of the AudienceHero project.
 *
 * (c) Marc Weistroff <marc@weistroff.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AudienceHero\Bundle\ContactImportCsvBundle\Tests\Queue;

use AudienceHero\Bundle\ContactImportCsvBundle\Queue\ContactImportCsvMessage;
use AudienceHero\Bundle\CoreBundle\Entity\TextStore;
use PHPUnit\Framework\TestCase;

class ContactImportCsvMessageTest extends TestCase
{
    public function testAccessors()
    {
        $message = ContactImportCsvMessage::create();
        $this->assertInstanceOf(ContactImportCsvMessage::class, $message);
        $textStore = new TextStore();
        $this->assertSame($message, $message->setTextStore($textStore));
        $this->assertSame($textStore, $message->getTextStore());
    }
}
