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

namespace AudienceHero\Bundle\ActivityBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * RefererTrait.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
trait RefererTrait
{
    /**
     * @var null|string
     *
     * @ORM\Column(name="referer", type="string", length=4096, nullable=true)
     * @Groups({"read"})
     */
    private $referer;

    /**
     * @var null|string
     *
     * @ORM\Column(name="referer_medium", type="string", length=64, nullable=true)
     * @Groups({"read"})
     */
    private $refererMedium;

    /**
     * @var null|string
     *
     * @ORM\Column(name="referer_source", type="string", length=255, nullable=true)
     * @Groups({"read"})
     */
    private $refererSource;

    /**
     * @var null|string
     *
     * @ORM\Column(name="referer_search_term", type="string", length=4096, nullable=true)
     * @Groups({"read"})
     */
    private $refererSearchTerm;

    public function setReferer(?string $referer): void
    {
        $this->referer = $referer;
    }

    public function getReferer(): ?string
    {
        return $this->referer;
    }

    public function setRefererMedium(?string $refererMedium): void
    {
        $this->refererMedium = $refererMedium;
    }

    public function getRefererMedium(): ?string
    {
        return $this->refererMedium;
    }

    public function setRefererSource(?string $refererSource): void
    {
        $this->refererSource = $refererSource;
    }

    public function getRefererSource(): ?string
    {
        return $this->refererSource;
    }

    public function setRefererSearchTerm(?string $refererSearchTerm): void
    {
        $this->refererSearchTerm = $refererSearchTerm;
    }

    public function getRefererSearchTerm(): ?string
    {
        return $this->refererSearchTerm;
    }
}
