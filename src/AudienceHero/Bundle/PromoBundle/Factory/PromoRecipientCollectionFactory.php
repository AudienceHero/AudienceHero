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

use AudienceHero\Bundle\MailingCampaignBundle\Factory\MailingRecipientCollectionFactory;
use AudienceHero\Bundle\PromoBundle\Entity\Promo;
use AudienceHero\Bundle\PromoBundle\Entity\PromoRecipient;

/**
 * PromoRecipientCollectionFactory.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
class PromoRecipientCollectionFactory
{
    /**
     * @var MailingRecipientCollectionFactory
     */
    private $factory;

    public function __construct(MailingRecipientCollectionFactory $factory)
    {
        $this->factory = $factory;
    }

    public function createCollection(Promo $promo): array
    {
        $collection = [];
        $mrs = $this->factory->createCollection($promo->getMailing());

        foreach ($mrs as $mr) {
            $pr = new PromoRecipient();
            $pr->setPromo($promo);
            $pr->setMailingRecipient($mr);

            $collection[] = $pr;
        }

        return $collection;
    }

    public function createBoostCollection(Promo $promo): array
    {
        $collection = [];
        $boostRecipients = $this->factory->createBoostCollection($promo->getMailing());

        /** @var PromoRecipient $promoRecipient */
        foreach ($promo->getRecipients() as $promoRecipient) {
            if (null !== $boostMr = $promoRecipient->getMailingRecipient()->getBoostMailingRecipient()) {
                $promoRecipient->setBoostMailingRecipient($boostMr);
                $collection[] = $promoRecipient;
            }
        }

        return $collection;
    }
}
