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

namespace AudienceHero\Bundle\MailingCampaignBundle\Tests\Action;

use AudienceHero\Bundle\CoreBundle\Bridge\Symfony\HttpFoundation\EmptyJsonLdResponse;
use AudienceHero\Bundle\MailingCampaignBundle\Action\PreviewAction;
use AudienceHero\Bundle\MailingCampaignBundle\Entity\Mailing;
use AudienceHero\Bundle\MailingCampaignBundle\Mailer\MailerInterface;
use PHPUnit\Framework\TestCase;

class PreviewActionTest extends TestCase
{
    public function testPreview()
    {
        $mailing = new Mailing();
        $mailing->setTestRecipient('foobar@example.com');

        $mailer = $this->prophesize(MailerInterface::class);
        $mailer->sendPreview($mailing, 'foobar@example.com')
               ->shouldBeCalled();

        $action = new PreviewAction($mailer->reveal());
        $this->assertInstanceOf(EmptyJsonLdResponse::class, $action($mailing));
    }
}
