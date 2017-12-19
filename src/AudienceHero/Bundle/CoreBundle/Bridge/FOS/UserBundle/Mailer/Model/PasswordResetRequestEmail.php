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

namespace AudienceHero\Bundle\CoreBundle\Bridge\FOS\UserBundle\Mailer\Model;

use AudienceHero\Bundle\CoreBundle\Bridge\Sylius\Mailer\Model\AbstractTransactionalEmail;

/**
 * ResettingEmail.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
final class PasswordResetRequestEmail extends AbstractTransactionalEmail
{
    public function getTemplate()
    {
        return 'AudienceHeroCoreBundle:mailer:fos_user_resetting_email.html.twig';
    }
}
