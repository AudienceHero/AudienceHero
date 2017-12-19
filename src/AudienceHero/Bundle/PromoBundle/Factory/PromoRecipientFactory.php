<?php

/*
 * This file is part of the AudienceHero project.
 *
 * (c) Marc Weistroff <marc@weistroff.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AudienceHero\Bundle\PromoBundle\Factory;
use AudienceHero\Bundle\PromoBundle\Entity\Promo;
use AudienceHero\Bundle\PromoBundle\Entity\PromoRecipient;
use Symfony\Component\Serializer\SerializerInterface;


/**
 * PromoRecipientFactory
 * @author Marc Weistroff <marc@weistroff.net>
 */
class PromoRecipientFactory
{
    /**
     * @var SerializerInterface
     */
    private $serializer;

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    public function createFromJson(string $json, Promo $promo): PromoRecipient
    {
        /** @var PromoRecipient $unserialized */
        $unserialized = $this->serializer->deserialize($json, PromoRecipient::class, 'json', [
            'item_operation_name' => 'put',
            'operation_type' => 'item',
            'api_allow_update' => false,
            'resource_class' => PromoRecipient::class,
            'groups' => ['write'],
        ]);

        // The collection gets populated with the unserialized element, that we don't want to save.
        $promo->getRecipients()->removeElement($unserialized);

        return $unserialized;
    }
}