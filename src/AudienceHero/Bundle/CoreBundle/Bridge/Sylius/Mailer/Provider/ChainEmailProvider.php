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

use Sylius\Component\Mailer\Model\EmailInterface;
use Sylius\Component\Mailer\Provider\EmailProviderInterface;

/**
 * ChainEmailProvider.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
class ChainEmailProvider implements EmailProviderInterface
{
    private $providers = [];

    public function registerProvider(EmailProviderInterface $provider)
    {
        if ($provider === $this) {
            return;
        }

        $this->providers[] = $provider;
    }

    /**
     * @param string $code
     *
     * @return EmailInterface
     */
    public function getEmail($code): EmailInterface
    {
        foreach ($this->providers as $provider) {
            try {
                return $provider->getEmail($code);
            } catch (\Exception $e) {
                // silence exception
            }
        }

        throw new \InvalidArgumentException(sprintf('No email with code %s found', $code));
    }
}
