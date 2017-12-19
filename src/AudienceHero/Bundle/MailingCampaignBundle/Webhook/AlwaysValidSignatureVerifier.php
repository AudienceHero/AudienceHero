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

/**
 * AlwaysValidSignatureVerifier.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
class AlwaysValidSignatureVerifier implements WebhookSignatureVerifierInterface
{
    public function isValid(array $request): bool
    {
        return true;
    }
}
