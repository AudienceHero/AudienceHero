<?php

/*
 * This file is part of the AudienceHero project.
 *
 * (c) Marc Weistroff <marc@weistroff.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AudienceHero\Bundle\CoreBundle\Tests\Bridge\Sylius\Mailer\Provider;

use AudienceHero\Bundle\CoreBundle\Bridge\Sylius\Mailer\Provider\ChainEmailProvider;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Mailer\Model\EmailInterface;
use Sylius\Component\Mailer\Provider\EmailProviderInterface;

class ChainEmailProviderTest extends TestCase
{
    /** @var ChainEmailProvider */
    private $chainProvider;

    public function setUp()
    {
        $this->chainProvider = new ChainEmailProvider();
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testGetEmailThrowsAnException()
    {
        $this->chainProvider->getEmail('foo');
    }

    public function testGetEmailSearchesProviders()
    {
        $p1 = $this->prophesize(EmailProviderInterface::class);
        $p1->getEmail('foo')->shouldBeCalled()->willThrow(new\InvalidArgumentException());
        $p2 = $this->prophesize(EmailProviderInterface::class);
        $p2->getEmail('foo')
           ->shouldBeCalled()
           ->willReturn($this->prophesize(EmailInterface::class)->reveal());

        $this->chainProvider->registerProvider($p1->reveal());
        $this->chainProvider->registerProvider($p2->reveal());

        $email = $this->chainProvider->getEmail('foo');
        $this->assertInstanceOf(EmailInterface::class, $email);
    }
}
