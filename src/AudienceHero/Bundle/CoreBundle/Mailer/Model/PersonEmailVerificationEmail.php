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

namespace AudienceHero\Bundle\CoreBundle\Mailer\Model;

use AudienceHero\Bundle\CoreBundle\Bridge\Sylius\Mailer\Model\AbstractTransactionalEmail;

/**
 * PersonEmailVerificationEmail.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
final class PersonEmailVerificationEmail extends AbstractTransactionalEmail
{
    public function getTemplate(): string
    {
        return 'AudienceHeroCoreBundle:mailer:person_email_verification.html.twig';
    }
}
