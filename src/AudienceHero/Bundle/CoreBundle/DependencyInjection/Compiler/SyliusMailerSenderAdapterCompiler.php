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

namespace AudienceHero\Bundle\CoreBundle\DependencyInjection\Compiler;

use AudienceHero\Bundle\CoreBundle\Bridge\Sylius\Mailer\Sender\Adapter\MailgunAdapter;
use Sylius\Component\Mailer\Sender\Adapter\AdapterInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class SyliusMailerSenderAdapterCompiler implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $adapter = $container->getParameter('audience_hero.bridge.sylius.sender_adapter');

        $alias = null;
        switch ($adapter) {
            case 'mailgun':
                $alias = MailgunAdapter::class;
                break;
            case 'swiftmailer':
                $alias = 'sylius.email_sender.adapter.swiftmailer';
                break;
            default:
                throw new \LogicException(sprintf('Unknown mailer adapter %s', $adapter));
        }

        $container->setAlias('sylius.mailer.sender_adapter', $alias);
        $container->setAlias(AdapterInterface::class, $alias);
    }
}
