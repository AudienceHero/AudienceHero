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

namespace AudienceHero\Bundle\CoreBundle\Action;

use AudienceHero\Bundle\CoreBundle\Bridge\FOS\UserBundle\Mailer\Model\PasswordResettedEmail;
use AudienceHero\Bundle\CoreBundle\Bridge\Symfony\HttpFoundation\EmptyJsonLdResponse;
use AudienceHero\Bundle\CoreBundle\Entity\User;
use AudienceHero\Bundle\CoreBundle\Mailer\TransactionalMailer;
use FOS\UserBundle\Doctrine\UserManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * ForgottenPasswordAction.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
class ResetPasswordAction
{
    /**
     * @var TransactionalMailer
     */
    private $mailer;
    /**
     * @var UserManager
     */
    private $userManager;

    public function __construct(UserManager $userManager, TransactionalMailer $mailer)
    {
        $this->mailer = $mailer;
        $this->userManager = $userManager;
    }

    /**
     * @Method("POST")
     * @Route(
     *     "/api/users/reset-password", name="users_reset_password",
     *     defaults={"_api_resource_class"=User::class, "_api_collection_operation_name"="reset_password"}
     * )
     */
    public function __invoke(User $data)
    {
        $confirmationToken = $data->getConfirmationToken();
        $plainPassword = $data->getPlainPassword();

        if (!$confirmationToken || !$plainPassword) {
            throw new BadRequestHttpException('Please provide a confirmationToken and a plainPassword');
        }

        $user = $this->userManager->findUserByConfirmationToken($confirmationToken);
        if (!$user) {
            throw new BadRequestHttpException('No user with this confirmationToken');
        }

        $user->setConfirmationToken(null);
        $user->setPlainPassword($plainPassword);
        $this->userManager->updateUser($user, true);
        $this->mailer->send(PasswordResettedEmail::class, $user, ['user' => $user], null);

        return new EmptyJsonLdResponse();
    }
}
