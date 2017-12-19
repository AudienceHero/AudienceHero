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

namespace AudienceHero\Bundle\ContactImportCsvBundle\Queue;

use AudienceHero\Bundle\CoreBundle\Entity\TextStore;
use AudienceHero\Bundle\CoreBundle\Queue\Producer;

/**
 * ContactCsvImportProducer.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
class ContactCsvImportProducer
{
    const IMPORT_CSV_CONTACT = 'audiencehero.import.csv.contact';

    /**
     * @var Producer
     */
    private $producer;

    public function __construct(Producer $producer)
    {
        $this->producer = $producer;
    }

    public function import(TextStore $textStore)
    {
        $this->producer->sendCommand(self::IMPORT_CSV_CONTACT, ContactImportCsvMessage::create()->setTextStore($textStore));
    }
}
