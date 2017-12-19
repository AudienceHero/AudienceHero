<?php

/*
 * This file is part of the AudienceHero project.
 *
 * (c) Marc Weistroff <marc@weistroff.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AudienceHero\Bundle\CoreBundle\Tests\Mailer;

use AudienceHero\Bundle\CoreBundle\Entity\User;
use AudienceHero\Bundle\CoreBundle\Mailer\TransactionalMailer;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;
use Sylius\Component\Mailer\Sender\SenderInterface;

class TransactionalMailerTest extends TestCase
{
    /** @var TransactionalMailer */
    private $mailer;
    /** @var ObjectProphecy */
    private $sender;

    public function setUp()
    {
        $this->sender = $this->prophesize(SenderInterface::class);
        $this->mailer = new TransactionalMailer($this->sender->reveal());
        $this->owner = new User();
        $this->owner->setUsername('username');
        $this->owner->setEmail('owner@example.com');
    }

    public function testSend()
    {
        $this->sender->send(
            'foo',
            ['foobar@example.com' => 'username'],
            [
                'key' => 'value',
                'owner' => $this->owner
            ]
        )->shouldBeCalled();

        $this->mailer->send('foo', $this->owner, ['key' => 'value'], 'foobar@example.com');
    }

    public function testSendWithoutToEmail()
    {
        $this->sender->send(
            'foo',
            ['owner@example.com' => 'username'],
            [
                'key' => 'value',
                'owner' => $this->owner
            ]
        )->shouldBeCalled();

        $this->mailer->send('foo', $this->owner, ['key' => 'value'], null);
    }
}
