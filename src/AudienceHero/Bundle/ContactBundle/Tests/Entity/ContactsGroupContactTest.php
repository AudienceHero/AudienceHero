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

namespace AudienceHero\Bundle\ContactBundle\Tests\Entity;

use AudienceHero\Bundle\ContactBundle\Entity\ContactsGroupContact;
use PHPUnit\Framework\TestCase;

class ContactsGroupContactTest extends TestCase
{
    private $cgc;

    public function setUp()
    {
        $this->cgc = new ContactsGroupContact();
    }

    public function testCancelUnsubscribe()
    {
        $now = new \DateTime();
        $this->cgc->setUnsubscribedAt($now);
        $this->assertSame($now, $this->cgc->getUnsubscribedAt());
        $this->cgc->resubscribe();
        $this->assertNull($this->cgc->getUnsubscribedAt());
    }

    public function testCancelCleaned()
    {
        $now = new \DateTime();
        $this->cgc->setCleanedAt($now);
        $this->cgc->setCleanedReason('foobar');
        $this->assertSame($now, $this->cgc->getCleanedAt());
        $this->assertSame('foobar', $this->cgc->getCleanedReason());
        $this->cgc->removeCleanState();
        $this->assertNull($this->cgc->getCleanedAt());
        $this->assertNull($this->cgc->getCleanedReason());
    }

    public function testIsSubscribed()
    {
        $this->assertTrue($this->cgc->isSubscribed());
        $this->assertFalse($this->cgc->isUnsubscribed());
        $this->cgc->setUnsubscribedAt(new \DateTime());
        $this->assertFalse($this->cgc->isSubscribed());
        $this->assertTrue($this->cgc->isUnsubscribed());
    }

    public function testIsCleaned()
    {
        $this->assertFalse($this->cgc->isCleaned());
        $this->cgc->setCleanedAt(new \DateTime());
        $this->assertTrue($this->cgc->isCleaned());
    }

    public function testIsOptin()
    {
        $this->assertFalse($this->cgc->isOptin());
        $this->cgc->setOptinAt(new \DateTime());
        $this->assertTrue($this->cgc->isOptin());
    }

    /**
     * @dataProvider provideAcceptEmails
     */
    public function testAcceptEmails($optin, $unsubscribed, $cleaned, $result)
    {
        if ($optin) {
            $this->cgc->setOptinAt($optin);
        }
        if ($unsubscribed) {
            $this->cgc->setUnsubscribedAt($unsubscribed);
        }
        if ($cleaned) {
            $this->cgc->setCleanedAt($cleaned);
        }

        $this->assertSame($result, $this->cgc->acceptEmails());
    }

    public function provideAcceptEmails()
    {
        $yes = new \DateTime();

        return [
            // optin, unsubscribe, cleaned, result
            [$yes, null, null, true],
            [null, null, null, false],
            [null, $yes, null, false],
            [null, null, $yes, false],
            [null, $yes, $yes, false],
            [$yes, $yes, null, false],
            [$yes, null, $yes, false],
            [$yes, $yes, $yes, false],
        ];
    }
}
