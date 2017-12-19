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

use AudienceHero\Bundle\ContactImportCsvBundle\Queue\ContactCsvImportProducer;
use AudienceHero\Bundle\ContactImportCsvBundle\Queue\ContactImportCsvMessage;
use AudienceHero\Bundle\CoreBundle\Entity\TextStore;
use AudienceHero\Bundle\CoreBundle\Queue\Producer;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;

class ContactCsvImportProducerTest extends TestCase
{
    public function testImport()
    {
        $coreProducer = $this->prophesize(Producer::class);

        $textStore = new TextStore();
        $coreProducer->sendCommand(ContactCsvImportProducer::IMPORT_CSV_CONTACT, Argument::that(function(ContactImportCsvMessage $message) use ($textStore) {
            return $message->getTextStore() === $textStore;
        }))->shouldBeCalled();

        $producer = new ContactCsvImportProducer($coreProducer->reveal());
        $producer->import($textStore);
    }
}
