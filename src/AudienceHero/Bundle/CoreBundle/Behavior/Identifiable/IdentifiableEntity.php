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

use Doctrine\Common\Util\Inflector;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * IdentifiableEntity.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
trait IdentifiableEntity
{
    /**
     * @var null|string
     *
     * @ORM\Id()
     * @ORM\Column(type="guid")
     * @ORM\GeneratedValue(strategy="UUID")
     * @Groups({"id", "read"})
     */
    protected $id;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getSoftReferenceKey(): string
    {
        $name = substr(static::class, strrpos(static::class, '\\') + 1);

        return Inflector::tableize(Inflector::pluralize($name));
    }
}
