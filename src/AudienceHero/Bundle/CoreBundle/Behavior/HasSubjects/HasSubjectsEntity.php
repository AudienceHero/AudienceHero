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

use AudienceHero\Bundle\CoreBundle\Behavior\HasSubjects\HasSubjectsInterface;
use AudienceHero\Bundle\CoreBundle\Behavior\Identifiable\IdentifiableInterface;
use AudienceHero\Bundle\CoreBundle\Behavior\Ownable\OwnableInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\MaxDepth;

/**
 * HasSubjectsEntity.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
trait HasSubjectsEntity
{
    /**
     * var null|string.
     *
     * @ORM\Column(type="jsonb_iri_associations", options={"jsonb": true}, nullable=true)
     * @MaxDepth(1)
     * @Groups({"read", "write"})
     */
    protected $subjects = [];

    public function getSubjects(): array
    {
        return $this->subjects;
    }

    /**
     * @param array $subjects
     */
    public function setSubjects(array $subjects): void
    {
        $this->subjects = $subjects;
    }

    public function addSubject(IdentifiableInterface $identifiable): HasSubjectsInterface
    {
        $this->subjects[$identifiable->getSoftReferenceKey()] = $identifiable;
        if (!$this->getOwner() && $identifiable instanceof OwnableInterface) {
            $this->setOwner($identifiable->getOwner());
        }

        return $this;
    }

    public function removeSubject(IdentifiableInterface $identifiable): HasSubjectsInterface
    {
        /**
         * @var string
         * @var IdentifiableInterface $value
         */
        foreach ($this->subjects as $key => $value) {
            if ($value->getId() === $identifiable->getId()) {
                unset($this->subjects[$key]);
            }
        }

        return $this;
    }

    public function getSubject(string $key): ?IdentifiableInterface
    {
        return $this->subjects[$key] ?: null;
    }
}
