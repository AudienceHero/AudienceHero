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

namespace AudienceHero\Bundle\PromoBundle\Mailer;

use AudienceHero\Bundle\PromoBundle\Entity\Promo;
use AudienceHero\Bundle\PromoBundle\Entity\PromoRecipient;

/**
 * MailerInterface.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
interface MailerInterface
{
    public function send(PromoRecipient $recipient, bool $isBoost);
    public function sendPreview(Promo $promo, string $to);
}
