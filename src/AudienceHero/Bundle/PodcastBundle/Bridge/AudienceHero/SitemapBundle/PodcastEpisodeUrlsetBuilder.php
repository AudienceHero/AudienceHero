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

namespace AudienceHero\Bundle\PodcastBundle\Bridge\AudienceHero\SitemapBundle;

use AudienceHero\Bundle\CoreBundle\Behavior\Publishable\PublishableInterface;
use AudienceHero\Bundle\PodcastBundle\Entity\PodcastEpisode;
use AudienceHero\Bundle\PodcastBundle\Repository\PodcastEpisodeRepository;
use AudienceHero\Bundle\SitemapBundle\Builder\UrlsetBuilderInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Thepixeldeveloper\Sitemap\Subelements\Image;
use Thepixeldeveloper\Sitemap\Url;
use Thepixeldeveloper\Sitemap\Urlset;

/**
 * PodcastEpisodeUrlsetBuilder.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
class PodcastEpisodeUrlsetBuilder implements UrlsetBuilderInterface
{
    /** @var PodcastEpisodeRepository */
    private $repository;
    /** @var UrlGeneratorInterface */
    private $router;

    public function __construct(PodcastEpisodeRepository $repository, UrlGeneratorInterface $router)
    {
        $this->repository = $repository;
        $this->router = $router;
    }

    public function getName(): string
    {
        return 'podcast-episode';
    }

    public function build(): Urlset
    {
        /** @var PodcastEpisode[] $entities */
        $entities = $this->repository->findByPrivacy(PublishableInterface::PRIVACY_PUBLIC);

        $urlset = new Urlset();
        foreach ($entities as $entity) {
            $url = new Url($this->router->generate('podcast_episodes_listen', [
                'username' => $entity->getChannel()->getOwner()->getUsername(),
                'slug' => $entity->getChannel()->getSlug(),
                'episodeSlug' => $entity->getSlug(),
            ], UrlGeneratorInterface::ABSOLUTE_URL));
            $url->setLastMod($entity->getUpdatedAt()->format('c'));

            $artwork = $entity->getArtwork() ? $entity->getArtwork() : $entity->getChannel()->getArtwork();
            $imageUrl = $this->router->generate('audience_hero_img_show_alt', ['url' => urlencode($artwork->getRemoteUrl()), 'size' => '600x0'], UrlGeneratorInterface::ABSOLUTE_URL);
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
