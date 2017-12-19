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

namespace AudienceHero\Bundle\MailingCampaignBundle\Tests\Webhook;

use AudienceHero\Bundle\MailingCampaignBundle\Webhook\MailgunWebhookSignatureVerifier;
use Mailgun\Api\Webhook;
use Mailgun\Mailgun;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;

class MailgunWebhookSignatureVerifierTest extends TestCase
{
    /** @var ObjectProphecy */
    private $mailgun;

    public function setUp()
    {
        $this->mailgun = $this->prophesize(Mailgun::class);
    }

    /**
     * @dataProvider provideTestIsValidThrowsExceptionIfKeysAreNotPresent
     * @expectedException \InvalidArgumentException
     */
    public function testIsValidThrowsExceptionIfKeysAreNotPresent(array $data)
    {
        $verifier = new MailgunWebhookSignatureVerifier($this->mailgun->reveal());
        $verifier->isValid($data);
    }

    public function testIsValid()
    {
        $data = [
            'timestamp' => 'my_timestamp',
            'token' => 'my_token',
            'signature' => 'my_signature',
        ];

        $webhook = $this->prophesize(Webhook::class);
        $webhook->verifyWebhookSignature('my_timestamp', 'my_token', 'my_signature')
                ->shouldBeCalled()
                ->willReturn(true);

        $this->mailgun->webhooks()->willReturn($webhook->reveal())->shouldBeCalled();
        $verifier = new MailgunWebhookSignatureVerifier($this->mailgun->reveal());
        $this->assertTrue($verifier->isValid($data));
    }

    public function provideTestIsValidThrowsExceptionIfKeysAreNotPresent()
    {
        return [
            [
                [],
            ],
            [
                ['timestamp' => 'foo'],
            ],
            [
                ['token' => 'foo'],
            ],
            [
                ['signature' => 'foo'],
            ],
        ];
    }
}
