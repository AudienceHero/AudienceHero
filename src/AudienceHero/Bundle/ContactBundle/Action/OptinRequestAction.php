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

namespace AudienceHero\Bundle\ContactBundle\Action;

use AudienceHero\Bundle\ContactBundle\Entity\ContactsGroupForm;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * FormOptinRequestAction.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
class OptinRequestAction
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
     * @Route("/forms/{id}", name="contacts_group_forms_optin_request")
     * @Route("/forms/{id}/request-confirm")
     * @Route("/forms/{id}/request-confirmed", name="contacts_group_forms_optin_confirmed")
     * @Method({"GET"})
     */
    public function __invoke(Request $request, ContactsGroupForm $contactsGroupForm)
    {
        return new Response(
            $this->twig->render('frontoffice.html.twig')
        );
    }
}
