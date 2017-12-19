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

namespace AudienceHero\Bundle\CoreBundle\Bridge\FOS\UserBundle\Mailer;

use AudienceHero\Bundle\CoreBundle\Bridge\FOS\UserBundle\Mailer\Model\PasswordResetRequestEmail;
use AudienceHero\Bundle\CoreBundle\Mailer\TransactionalMailer;
use FOS\UserBundle\Mailer\MailerInterface;
use FOS\UserBundle\Model\UserInterface;

/**
 * FOSUserMailer.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
class FOSUserMailer implements MailerInterface
{
    /**
     * @var TransactionalMailer
     */
    private $mailer;

    public function __construct(TransactionalMailer $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * Send an email to a user to confirm the account creation.
     *
     * @param UserInterface $user
     *
     * @deprecated you should use the TransactionMailer directly instead
     *
     * @throws \LogicException
     */
    public function sendConfirmationEmailMessage(UserInterface $user)
    {
        throw new \LogicException('Deprecated.');
    }

    /**
     * Send an email to a user to confirm the password reset.
     *
     * @param UserInterface $user
     */
    public function sendResettingEmailMessage(UserInterface $user)
    {
        $this->mailer->send(PasswordResetRequestEmail::class, $user, ['person' => $user], null);
    }
}
