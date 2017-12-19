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

namespace AudienceHero\Bundle\PromoBundle\Factory;

use AudienceHero\Bundle\CoreBundle\Generator\UUIDGenerator;
use AudienceHero\Bundle\PromoBundle\Entity\Promo;
use AudienceHero\Bundle\PromoBundle\Entity\PromoRecipient;
use AudienceHero\Bundle\PromoBundle\Mailer\Model\PromoCampaignEmail;

/**
 * PromoCampaignEmailFactory.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
class PromoCampaignEmailFactory
{
    /**
     * @var UUIDGenerator
     */
    private $generator;

    public function __construct(UUIDGenerator $generator)
    {
        $this->generator = $generator;
    }

    public function createClassic(Promo $promo, PromoRecipient $promoRecipient)
    {
        $mailing = $promo->getMailing();

        $email = new PromoCampaignEmail();
        $email->setSenderName($mailing->getFromName());
        $email->setSenderAddress($mailing->getPersonEmail()->getEmail());
        $email->setTemplate('AudienceHeroPromoBundle:mailer:classic.html.twig');
        $email->setEnabled(true);
        $email->setPromo($promo);
        $email->setMailing($mailing);
        $email->setPromoRecipient($promoRecipient);
        $email->setMailingRecipient($promoRecipient->getMailingRecipient());
        $email->setIdentifier($this->generator->generate());

        return $email;
    }

    public function createClassicBoost(Promo $promo, PromoRecipient $promoRecipient)
    {
        $mailing = $promo->getMailing();

        $email = new PromoCampaignEmail();
        $email->setSenderName($mailing->getFromName());
        $email->setSenderAddress($mailing->getPersonEmail()->getEmail());
        $email->setTemplate('AudienceHeroPromoBundle:mailer:classic_boost.html.twig');
        $email->setEnabled(true);
        $email->setPromo($promo);
        $email->setMailing($mailing);
        $email->setPromoRecipient($promoRecipient);
        $email->setMailingRecipient($promoRecipient->getBoostMailingRecipient());
        $email->setIdentifier($this->generator->generate());

        return $email;
    }
}
