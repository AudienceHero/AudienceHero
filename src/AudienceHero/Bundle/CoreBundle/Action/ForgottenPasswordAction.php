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

use AudienceHero\Bundle\CoreBundle\Bridge\Symfony\HttpFoundation\EmptyJsonLdResponse;
use AudienceHero\Bundle\CoreBundle\Entity\User;
use FOS\UserBundle\Doctrine\UserManager;
use FOS\UserBundle\Mailer\MailerInterface;
use FOS\UserBundle\Util\TokenGenerator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * ForgottenPasswordAction.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
class ForgottenPasswordAction
{
    /**
     * @var MailerInterface
     */
    private $mailer;
    /**
     * @var UserManager
     */
    private $userManager;
    /**
     * @var TokenGenerator
     */
    private $tokenGenerator;

    public function __construct(UserManager $userManager, MailerInterface $mailer, TokenGenerator $tokenGenerator)
    {
        $this->mailer = $mailer;
        $this->userManager = $userManager;
        $this->tokenGenerator = $tokenGenerator;
    }

    /**
     * @Method("POST")
     * @Route(
     *     "/api/users/forgotten-password", name="users_forgotten_password",
     *     defaults={"_api_resource_class"=User::class, "_api_collection_operation_name"="forgotten_password"}
     * )
     */
    public function __invoke(User $data)
    {
        $email = $data->getEmail();

        $real = $this->userManager->findUserByUsernameOrEmail($email);
        if ($real) {
            if (!$real->getConfirmationToken()) {
                $real->setConfirmationToken($this->tokenGenerator->generateToken());
            }
            $real->setPasswordRequestedAt(new \DateTime());
            $this->userManager->updateUser($real, true);
            $this->mailer->sendResettingEmailMessage($real);
        }

        return new EmptyJsonLdResponse();
    }
}
