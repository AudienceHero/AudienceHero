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

namespace AudienceHero\Bundle\PromoBundle\Action;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;

/**
 * PreviewAction.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
class PreviewAction
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
     * @Route("/promos/{id}/preview", name="promos_preview")
     * @Route("/promos/{id}/{recipientId}", name="promos_listen")
     */
    public function __invoke()
    {
        return new Response(
            $this->twig->render('frontoffice.html.twig')
        );
    }
}
