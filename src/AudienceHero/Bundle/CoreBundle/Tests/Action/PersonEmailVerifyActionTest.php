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

namespace AudienceHero\Bundle\CoreBundle\Tests\Action;

use AudienceHero\Bundle\CoreBundle\Action\PersonEmailVerifyAction;
use AudienceHero\Bundle\CoreBundle\Entity\PersonEmail;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class PersonEmailVerifyActionTest extends TestCase
{
    public function testNothingHappensIfPersonEmailIsAlreadyVerified()
    {
        $mock = $this->prophesize(PersonEmail::class);
        $mock->getIsVerified()->willReturn(true)->shouldBeCalled();
        $mock->getToken()->shouldNotBeCalled();
        $mock->getConfirmationToken()->shouldNotBeCalled();
        $mock->setConfirmationToken()->shouldNotBeCalled();
        $mock->setIsVerified(true)->shouldNotBeCalled();

        $action = new PersonEmailVerifyAction();

        $pe = $mock->reveal();

        $response = $action($pe);
        $this->assertSame($pe, $response);
    }

    public function testExceptionIsThrownIfTokenMismatch()
    {
        $this->expectException(BadRequestHttpException::class);

        $mock = $this->prophesize(PersonEmail::class);
        $mock->getIsVerified()->willReturn(false)->shouldBeCalled();
        $mock->getToken()->willReturn('sent')->shouldBeCalled();
        $mock->getConfirmationToken()->willReturn('mismatch')->shouldBeCalled();
        $mock->setConfirmationToken()->shouldNotBeCalled();
        $mock->setIsVerified(true)->shouldNotBeCalled();

        $action = new PersonEmailVerifyAction();

        $pe = $mock->reveal();

        $response = $action($pe);
        $this->assertSame($pe, $response);
    }

    public function testPersonEmailIsSetToVerifiedIfTokenMatch()
    {
        $mock = $this->prophesize(PersonEmail::class);
        $mock->getIsVerified()->willReturn(false)->shouldBeCalled();
        $mock->getToken()->willReturn('sent')->shouldBeCalled();
        $mock->getConfirmationToken()->willReturn('sent')->shouldBeCalled();
        $mock->setConfirmationToken(null)->shouldBeCalled();
        $mock->setIsVerified(true)->shouldBeCalled();

        $action = new PersonEmailVerifyAction();

        $pe = $mock->reveal();

        $response = $action($pe);
        $this->assertSame($pe, $response);
    }
}
