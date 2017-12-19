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

namespace AudienceHero\Bundle\CoreBundle\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use AudienceHero\Bundle\CoreBundle\Behavior\Identifiable\IdentifiableEntity;
use AudienceHero\Bundle\CoreBundle\Behavior\Identifiable\IdentifiableInterface;
use AudienceHero\Bundle\CoreBundle\Behavior\Ownable\OwnableEntity;
use AudienceHero\Bundle\CoreBundle\Behavior\Ownable\OwnableInterface;
use AudienceHero\Bundle\CoreBundle\Behavior\Timestampable\TimestampableEntity;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity as AssertUniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @AssertUniqueEntity(fields={"email", "owner"}, groups={"Default"})
 * @ORM\Entity(repositoryClass="AudienceHero\Bundle\CoreBundle\Repository\PersonEmailRepository")
 * @ORM\Table(name="ah_person_email")
 * @ApiResource(
 *     attributes={
 *         "normalization_context"={"groups"={"read"}},
 *         "filters"={"audience_hero.api.order.timestampable", "audience_hero.person_email.boolean"},
 *     },
 *     collectionOperations={
 *         "get"={"method"="GET"},
 *         "post"={"method"="POST"},
 *     },
 *     itemOperations={
 *         "get"={"method"="GET"},
 *         "delete"={"method"="DELETE"},
 *         "send_verification_email"={"route_name"="api_person_emails_send_verification_email"},
 *     }
 * )
 */
class PersonEmail implements OwnableInterface, IdentifiableInterface
{
    use OwnableEntity;
    use TimestampableEntity;
    use IdentifiableEntity;

    /**
     * @var null|string
     * @ORM\Column(type="string", length=255, nullable=false)
     * @Groups({"read"})
     *
     * @Assert\NotBlank
     * @Assert\Email
     */
    private $email;

    /**
     * @var bool
     * @Groups({"read"})
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $isVerified = false;

    /**
     * @var null|string
     * @ORM\Column(type="guid", nullable=true)
     */
    private $confirmationToken;

    /**
     * @var null|string
     * @Groups({"write"}))
     */
    private $token;

    public function __construct()
    {
        $this->setConfirmationToken(Uuid::uuid4()->toString());
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setIsVerified(bool $isVerified): void
    {
        $this->isVerified = $isVerified;
    }

    /**
     * @return bool
     */
    public function getIsVerified(): bool
    {
        return $this->isVerified;
    }

    /**
     * @return null|string
     */
    public function getConfirmationToken()
    {
        return $this->confirmationToken;
    }

    /**
     * @param null|string $confirmationToken
     */
    public function setConfirmationToken($confirmationToken)
    {
        $this->confirmationToken = $confirmationToken;
    }

    /**
     * @return null|string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @param null|string $token
     */
    public function setToken($token)
    {
        $this->token = $token;
    }
}
