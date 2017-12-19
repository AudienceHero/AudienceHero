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

namespace AudienceHero\Bundle\PromoBundle\Tests\Action;

use AudienceHero\Bundle\CoreBundle\Bridge\Symfony\HttpFoundation\EmptyJsonLdResponse;
use AudienceHero\Bundle\PromoBundle\Action\SendPreviewAction;
use AudienceHero\Bundle\PromoBundle\Entity\Promo;
use AudienceHero\Bundle\PromoBundle\Mailer\MailerInterface;
use PHPUnit\Framework\TestCase;

class SendPreviewActionTest extends TestCase
{
    public function testAction()
    {
        $promo = new Promo();
        $promo->setTestRecipient('foobar@example.com');
        $mailer = $this->prophesize(MailerInterface::class);
        $mailer->sendPreview($promo, 'foobar@example.com')->shouldBeCalled();

        $action = new SendPreviewAction($mailer->reveal());
        $response = $action($promo);
        $this->assertInstanceOf(EmptyJsonLdResponse::class, $response);
    }
}
