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

use AudienceHero\Bundle\PromoBundle\Entity\Promo;
use AudienceHero\Bundle\PromoBundle\Entity\PromoRecipient;
use AudienceHero\Bundle\PromoBundle\Queue\PromoMessage;
use PHPUnit\Framework\TestCase;

class PromoMessageTest extends TestCase
{
    public function testAccessors()
    {
        /** @var PromoMessage $message */
        $message = PromoMessage::create();
        $this->assertInstanceOf(PromoMessage::class, $message);

        $this->assertNull($message->getPromoRecipient());
        $this->assertNull($message->getPromo());
        $this->assertFalse($message->isBoost());

        $promo = new Promo();
        $promoRecipient = new PromoRecipient();

        $this->assertSame($message, $message->setPromo($promo));
        $this->assertSame($message, $message->setPromoRecipient($promoRecipient));
        $this->assertSame($message, $message->setBoost(true));

        $this->assertSame($promo, $message->getPromo());
        $this->assertSame($promoRecipient, $message->getPromoRecipient());
        $this->assertTrue($message->isBoost());
    }
}
