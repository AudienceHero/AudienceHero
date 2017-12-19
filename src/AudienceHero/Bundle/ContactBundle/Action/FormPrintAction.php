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
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Response;

/**
 * FormPrintAction.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
class FormPrintAction
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
     * @Route("/contacts_group_forms/{id}/print", name="contacts_group_forms_print")
     * @Method({"GET"})
     * @Security("is_granted('IS_AUTHENTICATED_ANONYMOUSLY')")
     */
    public function __invoke(ContactsGroupForm $cgf)
    {
        return new Response(
            $this->twig->render(
                '@AudienceHeroContact/form/print.html.twig',
                [
                    'contactsGroupForm' => $cgf,
                ]
            )
        );
    }
}
