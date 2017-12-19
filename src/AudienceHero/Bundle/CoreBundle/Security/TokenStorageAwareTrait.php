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

/**
 * TokenStorageAware trait.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
trait TokenStorageAwareTrait
{
    /** @var TokenStorageInterface */
    protected $tokenStorage;

    /**
     * Sets the token storage.
     *
     * @param TokenStorageInterface $tokenStorage
     */
    public function setTokenStorage(TokenStorageInterface $tokenStorage): void
    {
        $this->tokenStorage = $tokenStorage;
    }

    public function getUserFromTokenStorage(): ?Person
    {
        $token = $this->tokenStorage->getToken();
        if (!$token) {
            return null;
        }

        return $token->getUser();
    }
}
