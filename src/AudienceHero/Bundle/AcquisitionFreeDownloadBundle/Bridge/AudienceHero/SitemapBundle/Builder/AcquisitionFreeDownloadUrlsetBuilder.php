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

namespace AudienceHero\Bundle\AcquisitionFreeDownloadBundle\Bridge\AudienceHero\SitemapBundle\Builder;

use AudienceHero\Bundle\AcquisitionFreeDownloadBundle\Entity\AcquisitionFreeDownload;
use AudienceHero\Bundle\AcquisitionFreeDownloadBundle\Repository\AcquisitionFreeDownloadRepository;
use AudienceHero\Bundle\CoreBundle\Behavior\Publishable\Repository\Specification\PublishablePublic;
use AudienceHero\Bundle\SitemapBundle\Builder\UrlsetBuilderInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Thepixeldeveloper\Sitemap\Subelements\Image;
use Thepixeldeveloper\Sitemap\Url;
use Thepixeldeveloper\Sitemap\Urlset;

/**
 * AcquisitionFreeDownloadUrlsetBuilder.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
class AcquisitionFreeDownloadUrlsetBuilder implements UrlsetBuilderInterface
{
    /** @var \AudienceHero\Bundle\AcquisitionFreeDownloadBundle\Repository\AcquisitionFreeDownloadRepository */
    private $repository;
    /** @var UrlGeneratorInterface */
    private $router;

    public function __construct(AcquisitionFreeDownloadRepository $repository, UrlGeneratorInterface $router)
    {
        $this->repository = $repository;
        $this->router = $router;
    }

    public function getName(): string
    {
        return 'acquisition-free-download';
    }

    public function build(): Urlset
    {
        /** @var AcquisitionFreeDownload[] $entities */
        $entities = $this->repository->match(new PublishablePublic());

        $urlset = new Urlset();
        foreach ($entities as $entity) {
            $url = new Url($this->router->generate('acquisition_free_downloads_listen', ['username' => $entity->getOwner()->getUsername(), 'slug' => $entity->getSlug()], UrlGeneratorInterface::ABSOLUTE_URL));
            $url->setLastMod($entity->getUpdatedAt()->format('c'));

            $imageUrl = $this->router->generate('audience_hero_img_show_alt', ['url' => urlencode($entity->getArtwork()->getRemoteUrl()), 'size' => '600x0'], UrlGeneratorInterface::ABSOLUTE_URL);
            if (false === strpos($imageUrl, '://')) {
                $imageUrl = 'https:'.$imageUrl;
            }

            $image = new Image($imageUrl);
            $image->setCaption($entity->getTitle());
            $url->addSubElement($image);

            $urlset->addUrl($url);
        }

        return $urlset;
    }
}
