<?php

/*
 * This file is part of the AudienceHero project.
 *
 * (c) Marc Weistroff <marc@weistroff.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AudienceHero\Bundle\PromoBundle\Tests\Queue;

use AudienceHero\Bundle\CoreBundle\Entity\User;
use AudienceHero\Bundle\CoreBundle\Queue\Producer;
use AudienceHero\Bundle\PromoBundle\Entity\Promo;
use AudienceHero\Bundle\PromoBundle\Entity\PromoRecipient;
use AudienceHero\Bundle\PromoBundle\Queue\PromoMessage;
use AudienceHero\Bundle\PromoBundle\Queue\PromoProducer;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;

class PromoProducerTest extends TestCase
{
    private $coreProducer;
    private $producer;
    private $promo;
    private $promoRecipient;
    private $owner;

    public function setUp()
    {
        $this->coreProducer = $this->prophesize(Producer::class);
        $this->producer = new PromoProducer($this->coreProducer->reveal());
        $this->owner = new User();
        $this->promo = new Promo();
        $this->promo->setOwner($this->owner);
        $this->promoRecipient = new PromoRecipient();
        $this->promoRecipient->setOwner($this->owner);
        $this->promoRecipient->setPromo($this->promo);
    }

    public function testSendPromo()
    {
        $this->coreProducer->sendCommand(
            PromoProducer::PROMO_SEND,
            Argument::that(function(PromoMessage $message) {
                return
                    $message->getPromo() === $this->promo &&
                    $message->getPromoRecipient() === null &&
                    $message->isBoost() === false
                ;
            })
        )->shouldBeCalled();

        $this->producer->sendPromo($this->promo);
    }

    public function testBoostPromo()
    {
        $this->coreProducer->sendCommand(
            PromoProducer::PROMO_SEND,
            Argument::that(function(PromoMessage $message) {
                return
                    $message->getPromo() === $this->promo &&
                    $message->getPromoRecipient() === null &&
                    $message->isBoost() === true
                    ;
            })
        )->shouldBeCalled();

        $this->producer->boostPromo($this->promo);
    }

    public function testSendPromoRecipient()
    {
        $this->coreProducer->sendCommand(
            PromoProducer::PROMO_RECIPIENT_SEND,
            Argument::that(function(PromoMessage $message) {
                return
                    $message->getPromo() === $this->promo &&
                    $message->getPromoRecipient() === $this->promoRecipient &&
                    $message->isBoost() === false
                    ;
            })
        )->shouldBeCalled();

        $this->producer->sendPromoRecipient($this->promoRecipient);
    }

    public function testBoostPromoRecipient()
    {
        $this->coreProducer->sendCommand(
            PromoProducer::PROMO_RECIPIENT_SEND,
            Argument::that(function(PromoMessage $message) {
                return
                    $message->getPromo() === $this->promo &&
                    $message->getPromoRecipient() === $this->promoRecipient &&
                    $message->isBoost() === true
                    ;
            })
        )->shouldBeCalled();

        $this->producer->boostPromoRecipient($this->promoRecipient);
    }
}
