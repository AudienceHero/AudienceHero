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

namespace AudienceHero\Bundle\ContactBundle\Mailer\Model;

use AudienceHero\Bundle\CoreBundle\Bridge\Sylius\Mailer\Model\AbstractTransactionalEmail;

/**
 * OptinRequestEmail.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
class OptinRequestEmail extends AbstractTransactionalEmail
{
    public function getTemplate(): string
    {
        return 'AudienceHeroContactBundle:mailer:optin_request.html.twig';
    }
}
