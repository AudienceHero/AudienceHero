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

namespace AudienceHero\Bundle\PromoBundle\Mailer\Model;

use AudienceHero\Bundle\CoreBundle\Bridge\Sylius\Mailer\Model\IdentifiableEmailInterface;
use AudienceHero\Bundle\CoreBundle\Bridge\Sylius\Mailer\Model\IdentifiableEmailTrait;
use AudienceHero\Bundle\CoreBundle\Bridge\Sylius\Mailer\Model\TaggableEmailInterface;
use AudienceHero\Bundle\CoreBundle\Bridge\Sylius\Mailer\Model\TrackableEmailInterface;
use AudienceHero\Bundle\MailingCampaignBundle\Mailer\Model\MailingCampaignEmail;
use AudienceHero\Bundle\PromoBundle\Entity\Promo;
use AudienceHero\Bundle\PromoBundle\Entity\PromoRecipient;

/**
 * PromoCampaignEmail.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
class PromoCampaignEmail extends MailingCampaignEmail
{
    use IdentifiableEmailTrait;

    /** @var Promo */
    private $promo;

    /** @var PromoRecipient */
    private $promoRecipient;

    public function getTags(): array
    {
        return ['promo_campaign'];
    }

    /**
     * @return Promo
     */
    public function getPromo(): Promo
    {
        return $this->promo;
    }

    /**
     * @param Promo $promo
     */
    public function setPromo(Promo $promo)
    {
        $this->promo = $promo;
    }

    /**
     * @return PromoRecipient
     */
    public function getPromoRecipient(): PromoRecipient
    {
        return $this->promoRecipient;
    }

    /**
     * @param PromoRecipient $promoRecipient
     */
    public function setPromoRecipient(PromoRecipient $promoRecipient)
    {
        $this->promoRecipient = $promoRecipient;
    }
}
