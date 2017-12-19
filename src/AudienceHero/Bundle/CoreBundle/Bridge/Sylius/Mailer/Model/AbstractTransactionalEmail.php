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

namespace AudienceHero\Bundle\CoreBundle\Bridge\Sylius\Mailer\Model;

use Sylius\Component\Mailer\Model\Email;

/**
 * AbstractTransactionalEmail.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
abstract class AbstractTransactionalEmail extends Email implements TransactionalEmailInterface
{
    final public function getCode(): string
    {
        return static::class;
    }

    public function getSenderName(): string
    {
        return 'AudienceHero';
    }

    public function getSenderAddress(): string
    {
        return 'noreply@audiencehero.org';
    }
}
