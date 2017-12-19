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

namespace AudienceHero\Bundle\CoreBundle\Action;

use AudienceHero\Bundle\CoreBundle\Bridge\Symfony\HttpFoundation\EmptyJsonLdResponse;
use AudienceHero\Bundle\CoreBundle\Entity\TextStore;
use AudienceHero\Bundle\CoreBundle\Importer\TextStoreChainImporter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * TextStoreImportAction.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
class TextStoreImportAction
{
    /**
     * @var TextStoreChainImporter
     */
    private $chainImporter;

    public function __construct(TextStoreChainImporter $chainImporter)
    {
        $this->chainImporter = $chainImporter;
    }

    /**
     * @Route("/api/text_stores/{id}/import",
     *        name="api_text_stores_import",
     *        defaults={"_api_resource_class"=TextStore::class, "_api_item_operation_name"="import"})
     * @Method("PUT")
     * @Security("is_granted('IS_OWNER', data)")
     */
    public function __invoke(TextStore $data)
    {
        $importer = $this->chainImporter->getImporterFor($data);
        if (!$importer) {
            throw new BadRequestHttpException('No importer available.');
        }

        $importer->import($data);

        return new EmptyJsonLdResponse(202);
    }
}
