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

use AudienceHero\Bundle\CoreBundle\Bridge\Sylius\Mailer\Model\TransactionalEmailInterface;
use AudienceHero\Bundle\CoreBundle\Bridge\Sylius\Mailer\Provider\TransactionalEmailProvider;
use PHPUnit\Framework\TestCase;

class TransactionalEmailProviderTest extends TestCase
{
    /**
     * @expectedException InvalidArgumentException
     */
    public function testGetEmailThrowsExceptionIfKeyDoesNotExist()
    {
        $provider = new TransactionalEmailProvider();
        $provider->getEmail('foo');
    }

    public function testGetEmail()
    {
        $email = $this->prophesize(TransactionalEmailInterface::class);
        $email->getCode()->shouldBeCalled()->willReturn('foo');
        $double = $email->reveal();

        $provider = new TransactionalEmailProvider();
        $provider->registerEmail($double);
        $this->assertSame($double, $provider->getEmail('foo'));
    }
}
