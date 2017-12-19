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

use AudienceHero\Bundle\CoreBundle\Queue\Message;
use AudienceHero\Bundle\PromoBundle\Entity\Promo;
use AudienceHero\Bundle\PromoBundle\Entity\PromoRecipient;

/**
 * PromoMessage.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
class PromoMessage extends Message
{
    /**
     * @var Promo
     */
    private $promo;

    /**
     * @var PromoRecipient|null
     */
    private $promoRecipient;

    /**
     * @var bool
     */
    private $boost = false;

    /**
     * @return Promo
     */
    public function getPromo(): ?Promo
    {
        return $this->promo;
    }

    /**
     * @return PromoRecipient|null
     */
    public function getPromoRecipient(): ?PromoRecipient
    {
        return $this->promoRecipient;
    }

    /**
     * @param Promo $promo
     */
    public function setPromo(Promo $promo)
    {
        $this->promo = $promo;

        return $this;
    }

    /**
     * @param PromoRecipient|null $promoRecipient
     */
    public function setPromoRecipient(?PromoRecipient $promoRecipient)
    {
        $this->promoRecipient = $promoRecipient;

        return $this;
    }

    /**
     * @return bool
     */
    public function isBoost(): bool
    {
        return $this->boost;
    }

    /**
     * @param bool $boost
     */
    public function setBoost(bool $boost)
    {
        $this->boost = $boost;

        return $this;
    }
}
