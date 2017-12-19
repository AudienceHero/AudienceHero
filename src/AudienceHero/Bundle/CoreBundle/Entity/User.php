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
use Doctrine\ORM\Mapping as ORM;
use MarcW\Validator\Constraints\Subdomain;
use MarcW\Validator\Constraints\Username;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * User.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 * @ORM\Entity()
 * @ApiResource(
 *     attributes={
 *         "normalization_context"={"groups"={"read"}}
 *     },
 *     collectionOperations={
 *         "post"={"method"="POST"},
 *         "forgotten_password"={"route_name"="users_forgotten_password"},
 *         "reset_password"={"route_name"="users_reset_password"},
 *     },
 *     itemOperations={
 *         "get"={"method"="GET"},
 *         "put"={"method"="PUT"},
 *     }
 * )
 * @UniqueEntity("email")
 * @UniqueEntity("username")
 */
class User extends Person
{
    /**
     * @Groups({"read"})
     * @Username()
     * @Subdomain()
     */
    protected $username;

    /**
     * @Groups({"private_read"})
     */
    protected $email;

    /**
     * @Groups({"write"})
     */
    protected $plainPassword;

    public function setEmail($email)
    {
        parent::setEmail($email);

        return $this;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getSoftReferenceKey(): ?string
    {
        return 'users';
    }
}
