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
use AudienceHero\Bundle\PodcastBundle\Entity\PodcastChannel;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * PodcastChannelRouter.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
class PodcastChannelPopulator implements LinkablePopulatorInterface
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
        return $object instanceof PodcastChannel;
    }

    public function populate(LinkableInterface $object)
    {
        $object->setURL('rss_feed', $this->generator->generate('podcast_channels_feed', ['id' => $object->getId()], UrlGeneratorInterface::ABSOLUTE_URL));
        $object->setURL('public', $this->generator->generate('podcast_channels_listen', ['id' => $object->getId()], UrlGeneratorInterface::ABSOLUTE_URL));
    }
}
