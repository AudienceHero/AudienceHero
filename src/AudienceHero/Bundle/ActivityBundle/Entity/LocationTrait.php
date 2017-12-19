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
use Symfony\Component\Validator\Constraints as Assert;

/**
 * LocationTrait.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
trait LocationTrait
{
    /**
     * @var null|string
     * @ORM\Column(name="country", type="string", length=16, nullable=true)
     * @Assert\Length(max=16)
     * @Groups({"read"})
     */
    private $country;

    /**
     * @var null|string
     * @ORM\Column(name="city", type="string", length=255, nullable=true)
     * @Assert\Length(max=255)
     * @Groups({"read"})
     */
    private $city;

    /**
     * @var null|string
     * @Assert\Length(max=255)
     * @ORM\Column(name="region", type="string", length=255, nullable=true)
     * @Groups({"read"})
     */
    private $region;

    /**
     * @var null|float
     * @ORM\Column(name="latitude", type="decimal", precision=10, scale=7, nullable=true)
     * @Groups({"read"})
     */
    private $latitude;

    /**
     * @var null|float
     *
     * @ORM\Column(name="longitude", type="decimal", precision=10, scale=7, nullable=true)
     * @Groups({"read"})
     */
    private $longitude;

    public function setCountry(?string $country): void
    {
        $this->country = $country;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCity(?string $city): void
    {
        $this->city = $city;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setRegion(?string $region): void
    {
        $this->region = $region;
    }

    public function getRegion(): ?string
    {
        return $this->region;
    }

    public function setLatitude($latitude): void
    {
        $this->latitude = $latitude;
    }

    public function getLatitude()
    {
        return $this->latitude;
    }

    public function setLongitude($longitude): void
    {
        $this->longitude = $longitude;
    }

    public function getLongitude()
    {
        return $this->longitude;
    }
}
