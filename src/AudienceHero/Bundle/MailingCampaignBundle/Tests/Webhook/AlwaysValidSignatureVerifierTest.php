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

use AudienceHero\Bundle\MailingCampaignBundle\Webhook\AlwaysValidSignatureVerifier;
use PHPUnit\Framework\TestCase;

class AlwaysValidSignatureVerifierTest extends TestCase
{
    public function testIsValid()
    {
        $verifier = new AlwaysValidSignatureVerifier();
        $this->assertTrue($verifier->isValid([]));
    }
}
