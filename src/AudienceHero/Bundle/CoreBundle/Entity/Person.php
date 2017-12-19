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
use AudienceHero\Bundle\CoreBundle\Behavior\Identifiable\IdentifiableInterface;
use AudienceHero\Bundle\CoreBundle\Behavior\Metadata\HasPrivateMetadataInterface;
use AudienceHero\Bundle\CoreBundle\Behavior\Metadata\HasPrivateMetadataTrait;
use AudienceHero\Bundle\CoreBundle\Behavior\Metadata\HasPublicMetadataInterface;
use AudienceHero\Bundle\CoreBundle\Behavior\Metadata\HasPublicMetadataTrait;
use AudienceHero\Bundle\CoreBundle\Behavior\Timestampable\TimestampableEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * A person is an entity that have legal value in the system.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 * @ORM\Table(name="ah_person")
 * @ORM\Entity(repositoryClass="AudienceHero\Bundle\CoreBundle\Repository\PersonRepository")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="type", type="string", length=32)
 * @ORM\DiscriminatorMap({"user"="AudienceHero\Bundle\CoreBundle\Entity\User"})
 * @ApiResource(
 *     attributes={
 *         "normalization_context"={"groups"={"read"}, "enable_max_depth"=true}
 *     },
 *     collectionOperations={
 *         "get"={"method"="GET"},
 *     },
 *     itemOperations={
 *         "get"={"method"="GET"},
 *         "put"={"method"="PUT"},
 *     }
 * )
 */
abstract class Person extends BaseUser implements IdentifiableInterface, HasPrivateMetadataInterface, HasPublicMetadataInterface
{
    use TimestampableEntity;
    use HasPrivateMetadataTrait;
    use HasPublicMetadataTrait;

    /**
     * @var null|string
     *
     * @ORM\Id()
     * @ORM\Column(type="guid")
     * @ORM\GeneratedValue(strategy="UUID")
     * @Groups({"read"})
     */
    protected $id;

    public function __construct()
    {
        parent::__construct();

        $this->referrals = new ArrayCollection();

        // Enable account by default
        $this->setEnabled(true);
    }

    public function getId(): ?string
    {
        return $this->id;
    }
}
