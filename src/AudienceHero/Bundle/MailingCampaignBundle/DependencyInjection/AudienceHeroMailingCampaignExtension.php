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

namespace AudienceHero\Bundle\MailingCampaignBundle\DependencyInjection;

use AudienceHero\Bundle\MailingCampaignBundle\Mailer\Mailer;
use AudienceHero\Bundle\MailingCampaignBundle\Mailer\MailerInterface;
use AudienceHero\Bundle\MailingCampaignBundle\Webhook\AlwaysValidSignatureVerifier;
use AudienceHero\Bundle\MailingCampaignBundle\Webhook\MailgunWebhookSignatureVerifier;
use AudienceHero\Bundle\MailingCampaignBundle\Webhook\WebhookSignatureVerifierInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class AudienceHeroMailingCampaignExtension extends Extension
{
    /**
     * {@inheritdoc}
     *
     * @codeCoverageIgnore
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        $container->setAlias(MailerInterface::class, Mailer::class);
        if ('prod' === $container->getParameter('kernel.environment')) {
            $container->setAlias(WebhookSignatureVerifierInterface::class, MailgunWebhookSignatureVerifier::class);
        } else {
            $container->setAlias(WebhookSignatureVerifierInterface::class, AlwaysValidSignatureVerifier::class);
        }
    }
}
