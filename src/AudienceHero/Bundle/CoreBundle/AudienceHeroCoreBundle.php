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

namespace AudienceHero\Bundle\CoreBundle;

use AudienceHero\Bundle\CoreBundle\Behavior\Timestampable\Doctrine\DBAL\Types\UTCDateTimeType;
use AudienceHero\Bundle\CoreBundle\Behavior\Linkable\DependencyInjection\Compiler\LinkablePopulatorChainCompiler;
use AudienceHero\Bundle\CoreBundle\DependencyInjection\Compiler\SearcherCompiler;
use AudienceHero\Bundle\CoreBundle\DependencyInjection\Compiler\SyliusEmailProviderCompiler;
use AudienceHero\Bundle\CoreBundle\DependencyInjection\Compiler\SyliusMailerSenderAdapterCompiler;
use AudienceHero\Bundle\CoreBundle\DependencyInjection\Compiler\TextStoreImporterCompiler;
use AudienceHero\Bundle\CoreBundle\DependencyInjection\Compiler\TransactionalEmailCompiler;
use AudienceHero\Bundle\CoreBundle\DependencyInjection\Compiler\UserCheckerPass;
use AudienceHero\Bundle\CoreBundle\Behavior\HasSubjects\Doctrine\DBAL\Types\JsonbIriAssociations;
use AudienceHero\Bundle\CoreBundle\Behavior\Module\ModuleProviderInterface;
use Doctrine\DBAL\Types\Type;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class AudienceHeroCoreBundle extends Bundle implements ModuleProviderInterface
{
    public function __construct()
    {
        if (!Type::hasType('jsonb_iri_associations')) {
            Type::addType('jsonb_iri_associations', JsonbIriAssociations::class);
        }
        if (!Type::hasType('utc_datetime')) {
            Type::addType('utc_datetime', UTCDateTimeType::class);
        }
    }

    public function boot()
    {
        /** @var JsonbIriAssociations $type */
        $type = Type::getType('jsonb_iri_associations');
        $type->setIriConverter($this->container->get('audiencehero_core.api.iri_converter'));
    }

    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new UserCheckerPass());
        $container->addCompilerPass(new TransactionalEmailCompiler());
        $container->addCompilerPass(new SyliusEmailProviderCompiler());
        $container->addCompilerPass(new SyliusMailerSenderAdapterCompiler());
        $container->addCompilerPass(new LinkablePopulatorChainCompiler());
        $container->addCompilerPass(new TextStoreImporterCompiler());
    }

    public function getFrontOfficeModule(): ?string
    {
        return '@audiencehero-frontoffice/core';
    }

    public function getBackOfficeModule(): ?string
    {
        return '@audiencehero-backoffice/core';
    }
}
