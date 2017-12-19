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

namespace AudienceHero\Bundle\AcquisitionFreeDownloadBundle\Linkable;

use AudienceHero\Bundle\AcquisitionFreeDownloadBundle\Entity\AcquisitionFreeDownload;
use AudienceHero\Bundle\CoreBundle\Behavior\Linkable\LinkableInterface;
use AudienceHero\Bundle\CoreBundle\Behavior\Linkable\Populator\LinkablePopulatorInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * AcquisitionFreeDownloadLocatablePopulator.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
class AcquisitionFreeDownloadLinkablePopulator implements LinkablePopulatorInterface
{
    /**
     * @var UrlGeneratorInterface
     */
    private $router;

    public function __construct(UrlGeneratorInterface $router)
    {
        $this->router = $router;
    }

    public function supports(LinkableInterface $object): bool
    {
        return $object instanceof AcquisitionFreeDownload;
    }

    public function populate(LinkableInterface $object)
    {
        $object->setURL('preview', $this->router->generate('acquisition_free_downloads_preview', ['id' => $object->getId()], UrlGeneratorInterface::ABSOLUTE_URL));
        $object->setURL('public', $this->router->generate('acquisition_free_downloads_listen', ['id' => $object->getId()], UrlGeneratorInterface::ABSOLUTE_URL));
    }
}
