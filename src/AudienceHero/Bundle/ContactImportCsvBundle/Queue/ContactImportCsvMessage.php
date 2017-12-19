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
use AudienceHero\Bundle\CoreBundle\Queue\Message;

/**
 * ContactImportCsvMessage.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
class ContactImportCsvMessage extends Message
{
    /**
     * @var TextStore
     */
    private $textStore;

    /**
     * @return mixed
     */
    public function getTextStore(): ?TextStore
    {
        return $this->textStore;
    }

    /**
     * @param mixed $textStore
     */
    public function setTextStore(?TextStore $textStore): self
    {
        $this->textStore = $textStore;

        return $this;
    }
}
