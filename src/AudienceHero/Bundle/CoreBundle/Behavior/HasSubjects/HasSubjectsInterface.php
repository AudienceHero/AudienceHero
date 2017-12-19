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

namespace AudienceHero\Bundle\CoreBundle\Behavior\HasSubjects;

use AudienceHero\Bundle\CoreBundle\Behavior\Identifiable\IdentifiableInterface;

/**
 * HasSubjectsInterface.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
interface HasSubjectsInterface
{
    public function getSubjects(): array;

    public function setSubjects(array $subjects): void;

    public function getSubject(string $key): ?IdentifiableInterface;

    public function addSubject(IdentifiableInterface $identifiable): HasSubjectsInterface;

    public function removeSubject(IdentifiableInterface $identifiable): HasSubjectsInterface;
}
