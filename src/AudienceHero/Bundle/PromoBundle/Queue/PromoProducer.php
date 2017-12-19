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

namespace AudienceHero\Bundle\PromoBundle\Queue;

use AudienceHero\Bundle\CoreBundle\Queue\Producer;
use AudienceHero\Bundle\PromoBundle\Entity\Promo;
use AudienceHero\Bundle\PromoBundle\Entity\PromoRecipient;

/**
 * PromoProducer.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
class PromoProducer
{
    public const PROMO_SEND = 'audiencehero.promo.send';
    public const PROMO_RECIPIENT_SEND = 'audiencehero.promo.recipient.send';

    /**
     * @var Producer
     */
    private $producer;

    public function __construct(Producer $producer)
    {
        $this->producer = $producer;
    }

    public function sendPromo(Promo $promo)
    {
        $this->producer->sendCommand(self::PROMO_SEND, PromoMessage::create()->setPromo($promo));
    }

    public function sendPromoRecipient(PromoRecipient $promoRecipient)
    {
        $this->producer->sendCommand(self::PROMO_RECIPIENT_SEND, PromoMessage::create()->setPromo($promoRecipient->getPromo())->setPromoRecipient($promoRecipient));
    }

    public function boostPromo(Promo $promo)
    {
        $this->producer->sendCommand(self::PROMO_SEND, PromoMessage::create()->setPromo($promo)->setBoost(true));
    }

    public function boostPromoRecipient(PromoRecipient $promoRecipient)
    {
        $this->producer->sendCommand(self::PROMO_RECIPIENT_SEND, PromoMessage::create()->setPromo($promoRecipient->getPromo())->setPromoRecipient($promoRecipient)->setBoost(true));
    }
}
