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

namespace AudienceHero\Bundle\CoreBundle\Security;

use AudienceHero\Bundle\CoreBundle\Entity\Person;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

interface TokenStorageAwareInterface
{
    public function setTokenStorage(TokenStorageInterface $tokenStorage): void;

    public function getUserFromTokenStorage(): ?Person;
}
