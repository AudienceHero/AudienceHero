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

namespace AudienceHero\Bundle\CoreBundle\Bridge\Sylius\Mailer\Provider;

use AudienceHero\Bundle\CoreBundle\Bridge\Sylius\Mailer\Model\TransactionalEmailInterface;
use Sylius\Component\Mailer\Provider\EmailProviderInterface;
use Webmozart\Assert\Assert;

/**
 * AudienceHeroProvider.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
class TransactionalEmailProvider implements EmailProviderInterface
{
    private $emails = [];

    public function registerEmail(TransactionalEmailInterface $email)
    {
        $this->emails[$email->getCode()] = $email;
    }

    /**
     * @param string $code
     *
     * @return \Sylius\Component\Mailer\Model\EmailInterface
     */
    public function getEmail($code)
    {
        Assert::keyExists($this->emails, $code, sprintf('Email with code "%s" does not exist. Available codes are %s.', $code, implode(array_keys($this->emails, ', ', true))));

        return $this->emails[$code];
    }
}
