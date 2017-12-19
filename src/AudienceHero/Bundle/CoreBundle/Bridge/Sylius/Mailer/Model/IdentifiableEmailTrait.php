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

namespace AudienceHero\Bundle\CoreBundle\Bridge\Sylius\Mailer\Model;

/**
 * IdentifiableEmailTrait.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
trait IdentifiableEmailTrait
{
    /** @var string */
    private $identifier;

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function setIdentifier(string $id): void
    {
        $this->identifier = $id;
    }
}
