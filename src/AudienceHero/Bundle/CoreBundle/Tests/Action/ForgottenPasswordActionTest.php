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

use AudienceHero\Bundle\CoreBundle\Action\ForgottenPasswordAction;
use AudienceHero\Bundle\CoreBundle\Bridge\Symfony\HttpFoundation\EmptyJsonLdResponse;
use AudienceHero\Bundle\CoreBundle\Entity\User;
use FOS\UserBundle\Doctrine\UserManager;
use FOS\UserBundle\Mailer\MailerInterface;
use FOS\UserBundle\Util\TokenGenerator;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;

class ForgottenPasswordActionTest extends TestCase
{
    /** @var ObjectProphecy */
    private $mailer;
    /** @var ObjectProphecy */
    private $userManager;
    /** @var ObjectProphecy */
    private $tokenGenerator;

    public function setUp()
    {
        $this->userManager = $this->prophesize(UserManager::class);
        $this->tokenGenerator = $this->prophesize(TokenGenerator::class);
        $this->mailer = $this->prophesize(MailerInterface::class);
    }

    public function testActionWhenNoUserIsFound()
    {
        $user = new User();
        $user->setEmail('foo@example.com');

        $this->userManager->findUserByUsernameOrEmail('foo@example.com')->willReturn(null)->shouldBeCalled();
        $this->mailer->sendResettingEmailMessage(Argument::any())->shouldNotBeCalled();

        $action = new ForgottenPasswordAction($this->userManager->reveal(), $this->mailer->reveal(), $this->tokenGenerator->reveal());
        $response = $action($user);
        $this->assertInstanceOf(EmptyJsonLdResponse::class, $response);
    }

    public function testActionWhenAUserIsFound()
    {
        $user = new User();
        $user->setEmail('foo@example.com');

        $this->userManager->findUserByUsernameOrEmail('foo@example.com')->willReturn($user)->shouldBeCalled();
        $this->userManager->updateUser($user, true)->shouldBeCalled();
        $this->tokenGenerator->generateToken()->shouldBeCalled()->willReturn('0xdeadbeef');
        $this->mailer->sendResettingEmailMessage($user)->shouldBeCalled();

        $action = new ForgottenPasswordAction($this->userManager->reveal(), $this->mailer->reveal(), $this->tokenGenerator->reveal());
        $response = $action($user);
        $this->assertInstanceOf(EmptyJsonLdResponse::class, $response);
        $this->assertSame('0xdeadbeef', $user->getConfirmationToken());
        $this->assertInstanceOf(\DateTime::class, $user->getPasswordRequestedAt());
    }
}
