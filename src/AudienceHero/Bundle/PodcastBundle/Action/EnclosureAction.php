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

namespace AudienceHero\Bundle\PodcastBundle\Action;

use AudienceHero\Bundle\PodcastBundle\Entity\PodcastEpisode;
use AudienceHero\Bundle\PodcastBundle\Event\PodcastEvent;
use AudienceHero\Bundle\PodcastBundle\Event\PodcastEvents;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * EnclosureAction.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
class EnclosureAction
{
    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;
    /**
     * @var RegistryInterface
     */
    private $registry;

    public function __construct(EventDispatcherInterface $eventDispatcher, RegistryInterface $registry)
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->registry = $registry;
    }

    /**
     * @Route("/podcast_episodes/{id}.{extension}",
     *      name="podcast_episodes_enclosure",
     *      requirements={"id"="[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}"},
     * )
     * @Method("GET")
     */
    public function __invoke(PodcastEpisode $data)
    {
        $this->eventDispatcher->dispatch(
            PodcastEvents::EPISODE_ENCLOSURE_DOWNLOAD,
            new PodcastEvent($data->getChannel(), $data)
        );

        $this->registry->getManager()->flush();

        return new RedirectResponse($data->getFile()->getRemoteUrl());
    }
}
