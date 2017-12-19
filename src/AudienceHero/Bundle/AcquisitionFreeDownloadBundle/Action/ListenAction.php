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

namespace AudienceHero\Bundle\AcquisitionFreeDownloadBundle\Action;

use AudienceHero\Bundle\AcquisitionFreeDownloadBundle\Entity\AcquisitionFreeDownload;
use AudienceHero\Bundle\AcquisitionFreeDownloadBundle\Events\AcquisitionFreeDownloadEvent;
use AudienceHero\Bundle\AcquisitionFreeDownloadBundle\Events\AcquisitionFreeDownloadEvents;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * ListenAction.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
class ListenAction
{
    /**
     * @var \Twig_Environment
     */
    private $twig;
    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    public function __construct(\Twig_Environment $twig, EventDispatcherInterface $eventDispatcher)
    {
        $this->twig = $twig;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @Route("/free-downloads/{id}/preview", name="acquisition_free_downloads_preview")
     * @Route("/free-downloads/{id}", name="acquisition_free_downloads_listen")
     */
    public function __invoke(AcquisitionFreeDownload $acquisitionFreeDownload)
    {
        $this->eventDispatcher->dispatch(
            AcquisitionFreeDownloadEvents::HIT,
            AcquisitionFreeDownloadEvent::create()->setAcquisitionFreeDownload($acquisitionFreeDownload)
        );

        return new Response(
            $this->twig->render('frontoffice.html.twig')
        );
    }
}
