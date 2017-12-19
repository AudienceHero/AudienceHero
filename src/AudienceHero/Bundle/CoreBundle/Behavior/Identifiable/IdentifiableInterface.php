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

namespace AudienceHero\Bundle\CoreBundle\Behavior\Identifiable;

/**
 * IdentifiableInterface.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
interface IdentifiableInterface
{
    /**
     * Returns the universally unique identifier of the object.
     *
     * @return null|string
     */
    public function getId(): ?string;

    /**
     * @return null|string
     */
    public function getSoftReferenceKey(): ?string;
}
