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

namespace AudienceHero\Bundle\MailingCampaignBundle\Webhook;

use Mailgun\Mailgun;
use Webmozart\Assert\Assert;

/**
 * MailgunWebhookSignatureVerifier.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
class MailgunWebhookSignatureVerifier implements WebhookSignatureVerifierInterface
{
    /**
     * @var Mailgun
     */
    private $mailgun;

    public function __construct(Mailgun $mailgun)
    {
        $this->mailgun = $mailgun;
    }

    public function isValid(array $request): bool
    {
        Assert::keyExists($request, 'timestamp');
        Assert::keyExists($request, 'token');
        Assert::keyExists($request, 'signature');

        return $this->mailgun->webhooks()->verifyWebhookSignature($request['timestamp'], $request['token'], $request['signature']);
    }
}
