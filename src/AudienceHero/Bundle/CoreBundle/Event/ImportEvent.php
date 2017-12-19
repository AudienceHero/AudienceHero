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

namespace AudienceHero\Bundle\CoreBundle\Event;

use AudienceHero\Bundle\CoreBundle\Entity\TextStore;
use Symfony\Component\EventDispatcher\Event;

/**
 * ImportEvent.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
final class ImportEvent extends Event
{
    /**
     * @var TextStore
     */
    private $textStore;

    public function __construct(TextStore $textStore)
    {
        $this->textStore = $textStore;
    }

    /**
     * @return TextStore
     */
    public function getTextStore(): TextStore
    {
        return $this->textStore;
    }
}
