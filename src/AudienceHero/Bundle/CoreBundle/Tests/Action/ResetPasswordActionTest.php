<?php

/*
 * This file is part of the AudienceHero project.
 *
 * (c) Marc Weistroff <marc@weistroff.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AudienceHero\Bundle\CoreBundle\Tests\Action;

use AudienceHero\Bundle\CoreBundle\Action\ResetPasswordAction;
use AudienceHero\Bundle\CoreBundle\Bridge\FOS\UserBundle\Mailer\Model\PasswordResettedEmail;
use AudienceHero\Bundle\CoreBundle\Bridge\Symfony\HttpFoundation\EmptyJsonLdResponse;
use AudienceHero\Bundle\CoreBundle\Entity\User;
use AudienceHero\Bundle\CoreBundle\Mailer\TransactionalMailer;
use FOS\UserBundle\Doctrine\UserManager;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class ResetPasswordActionTest extends TestCase
{
    /** @var ObjectProphecy */
    private $mailer;
    /** @var ObjectProphecy */
    private $userManager;

    public function setUp()
    {
        $this->mailer = $this->prophesize(TransactionalMailer::class);
        $this->userManager = $this->prophesize(UserManager::class);
    }

    private function getInstance(): ResetPasswordAction
    {
        return new ResetPasswordAction(
            $this->userManager->reveal(),
            $this->mailer->reveal()
        );
    }

    /**
     * @expectedException Symfony\Component\HttpKernel\Exception\BadRequestHttpException
     * @expectedExceptionMessage Please provide a confirmationToken and a plainPassword
     */
    public function testInvokeWithoutData()
    {
        $action = $this->getInstance();
        $action(new User());
    }

    /**
     * @expectedException Symfony\Component\HttpKernel\Exception\BadRequestHttpException
     * @expectedExceptionMessage No user with this confirmationToken
     */
    public function testUserIsNotFound()
    {
        $user = new User();
        $user->setConfirmationToken('foobar');
        $user->setPlainPassword('password');

        $this->userManager->findUserByConfirmationToken('foobar')->shouldBeCalled()
            ->willReturn(null);

        $action = $this->getInstance();
        $action($user);
    }

    public function testInvoke()
    {
        $user = new User();
        $user->setConfirmationToken('foobar');
        $user->setPlainPassword('password');

        $dbUser = new User();

        $this->userManager->findUserByConfirmationToken('foobar')
            ->shouldBeCalled()
            ->willReturn($dbUser);

        $this->userManager->updateUser($dbUser, true)->shouldBeCalled();
        $this->mailer->send(PasswordResettedEmail::class, $dbUser, ['user' => $dbUser]);

        $action = $this->getInstance();
        $response = $action($user);
        $this->assertInstanceOf(EmptyJsonLdResponse::class, $response);

        $this->assertSame('password', $dbUser->getPlainPassword());
    }
}
