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

namespace AudienceHero\Bundle\PodcastBundle\Linkable;

use AudienceHero\Bundle\CoreBundle\Behavior\Linkable\LinkableInterface;
use AudienceHero\Bundle\CoreBundle\Behavior\Linkable\Populator\LinkablePopulatorInterface;
use AudienceHero\Bundle\PodcastBundle\Entity\PodcastEpisode;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * PodcastEpisodeRouter.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
class PodcastEpisodePopulator implements \AudienceHero\Bundle\CoreBundle\Behavior\Linkable\Populator\LinkablePopulatorInterface
{
    /**
     * @var UrlGeneratorInterface
     */
    private $generator;

    public function __construct(UrlGeneratorInterface $generator)
    {
        $this->generator = $generator;
    }

    public function supports(LinkableInterface $object): bool
    {
        return $object instanceof PodcastEpisode;
    }

    public function populate(LinkableInterface $object)
    {
        $object->setURL('public', $this->generator->generate('podcast_episodes_listen', ['id' => $object->getChannel()->getId(), 'episodeId' => $object->getId()], UrlGeneratorInterface::ABSOLUTE_URL));
    }
}
