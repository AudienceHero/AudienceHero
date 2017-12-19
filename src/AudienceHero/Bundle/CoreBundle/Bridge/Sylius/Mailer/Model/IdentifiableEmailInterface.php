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
 * IdentifiableEmailInterface.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
interface IdentifiableEmailInterface
{
    const ATTRIBUTE = 'audience_hero.identifier';

    public function setIdentifier(string $id): void;

    public function getIdentifier(): string;
}
