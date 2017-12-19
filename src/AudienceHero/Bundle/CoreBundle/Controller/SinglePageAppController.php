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

namespace AudienceHero\Bundle\CoreBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * SinglePageAppController.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
class SinglePageAppController extends Controller
{
    /**
     * @Route("/reset_password/{confirmationToken}", name="users_password_reset")
     * @Route("/person_emails/{id}/verify/{confirmationToken}", name="person_emails_verify")
     * @Route("/login", name="login")
     * @Route("/admin/{req}", name="admin_homepage", requirements={"req"=".*"}, defaults={"req"=""})
     */
    public function backofficeAction()
    {
        return $this->render('@AudienceHeroCore/default/backoffice.html.twig');
    }

    /**
     * @Route("/{req}", name="homepage", requirements={"req"=".*"}, defaults={"req"=""})
     */
    public function frontofficeAction()
    {
        return $this->render('@AudienceHeroCore/default/frontoffice.html.twig');
    }
}
