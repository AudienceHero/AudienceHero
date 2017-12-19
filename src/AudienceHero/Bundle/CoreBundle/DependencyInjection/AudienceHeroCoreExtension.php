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

namespace AudienceHero\Bundle\CoreBundle\DependencyInjection;

use ApiPlatform\Core\Api\IriConverterInterface;
use AudienceHero\Bundle\CoreBundle\Action\CountryListAction;
use AudienceHero\Bundle\CoreBundle\Action\LanguageListAction;
use AudienceHero\Bundle\CoreBundle\Behavior\Linkable\DependencyInjection\Compiler\LinkablePopulatorChainCompiler;
use AudienceHero\Bundle\CoreBundle\Bridge\FOS\UserBundle\Mailer\FOSUserMailer;
use AudienceHero\Bundle\CoreBundle\Bridge\Sylius\Mailer\Model\TransactionalEmailInterface;
use AudienceHero\Bundle\CoreBundle\Bridge\Sylius\Mailer\Sender\Adapter\MailgunAdapter;
use AudienceHero\Bundle\CoreBundle\DependencyInjection\Compiler\SyliusEmailProviderCompiler;
use AudienceHero\Bundle\CoreBundle\DependencyInjection\Compiler\TextStoreImporterCompiler;
use AudienceHero\Bundle\CoreBundle\DependencyInjection\Compiler\TransactionalEmailCompiler;
use AudienceHero\Bundle\CoreBundle\Importer\TextStoreImporterInterface;
use AudienceHero\Bundle\CoreBundle\Behavior\Linkable\Populator\LinkablePopulatorInterface;
use Enqueue\Client\ProducerInterface;
use FOS\UserBundle\Mailer\MailerInterface;
use Geocoder\Geocoder;
use GuzzleHttp\Client;
use Interop\Queue\PsrProcessor;
use Mailgun\Mailgun;
use Sylius\Component\Mailer\Provider\EmailProviderInterface;
use Sylius\Component\Mailer\Renderer\Adapter\AdapterInterface as RendererAdapterInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class AudienceHeroCoreExtension extends Extension
{
    /**
     * {@inheritdoc}
     *
     * @codeCoverageIgnore
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        $container->setAlias(ProducerInterface::class, 'enqueue.client.producer');
        $container->setAlias(Geocoder::class, 'bazinga_geocoder.geocoder');

        $container->setParameter('audience_hero.bridge.sylius.sender_adapter', $config['mailer']['adapter']);

        $container->getDefinition(Mailgun::class)->setArgument(0, $config['mailer']['mailgun']['api_key']);

        $container->getDefinition(MailgunAdapter::class)
            ->setArgument('$domain', $config['mailer']['mailgun']['domain'])
            ->setArgument('$isTestDelivery', $config['mailer']['mailgun']['is_test_delivery'])
        ;

        $container->registerForAutoconfiguration(LinkablePopulatorInterface::class)
                  ->addTag(LinkablePopulatorChainCompiler::TAG);

        $container->registerForAutoconfiguration(TransactionalEmailInterface::class)
                  ->addTag(TransactionalEmailCompiler::TAG);

        $container->registerForAutoconfiguration(EmailProviderInterface::class)
                  ->addTag(SyliusEmailProviderCompiler::TAG);

        $container->registerForAutoconfiguration(TextStoreImporterInterface::class)
                  ->addTag(TextStoreImporterCompiler::TAG);

        $container->setAlias(MailerInterface::class, FOSUserMailer::class);

        $container->setAlias(RendererAdapterInterface::class, 'sylius.email_renderer.adapter.twig');
        $container->setAlias(Client::class, 'httplug.client.default');

        $container->registerForAutoconfiguration(PsrProcessor::class)
                  ->addTag('enqueue.client.processor')
                  ->setPublic(true)
        ;

        $container->getDefinition(CountryListAction::class)
                  ->setArgument('$localeDir', sprintf('%s/vendor/umpirsky/country-list/data', $container->getParameter('kernel.project_dir')));

        $container->getDefinition(LanguageListAction::class)
            ->setArgument('$localeDir', sprintf('%s/vendor/umpirsky/language-list/data', $container->getParameter('kernel.project_dir')));

        $container->setAlias(IriConverterInterface::class, 'audiencehero_core.api.iri_converter');
    }
}
