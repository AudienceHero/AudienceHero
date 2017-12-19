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

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;

/**
 * ChannelListenAction.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
class ListenAction
{
    /**
     * @var \Twig_Environment
     */
    private $twig;

    public function __construct(\Twig_Environment $twig)
    {
        $this->twig = $twig;
    }

    /**
     * @Route("/podcasts/{id}", name="podcast_channels_listen")
     * @Route("/podcasts/{id}/{episodeId}", name="podcast_episodes_listen")
     */
    public function __invoke()
    {
        return new Response(
            $this->twig->render('frontoffice.html.twig')
        );
    }
}
