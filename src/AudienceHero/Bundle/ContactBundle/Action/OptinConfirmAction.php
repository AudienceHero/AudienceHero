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

use AudienceHero\Bundle\ContactBundle\Entity\ContactsGroupContact;
use AudienceHero\Bundle\ContactBundle\Entity\ContactsGroupForm;
use AudienceHero\Bundle\ContactBundle\Event\ContactEvent;
use AudienceHero\Bundle\ContactBundle\Event\ContactEvents;
use AudienceHero\Bundle\ContactBundle\Manager\OptManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig_Environment;

/**
 * OptinConfirm.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
class OptinConfirmAction
{
    /**
     * @var OptManager
     */
    private $optManager;
    /**
     * @var RegistryInterface
     */
    private $registry;
    /**
     * @var UrlGeneratorInterface
     */
    private $router;

    public function __construct(OptManager $optManager, RegistryInterface $registry, UrlGeneratorInterface $router)
    {
        $this->optManager = $optManager;
        $this->registry = $registry;
        $this->router = $router;
    }

    /**
     * @Route("/forms/{id}/optin-confirm/{cgcId}", name="contacts_group_forms_optin_confirm")
     * @ParamConverter(name="cgc", class=ContactsGroupContact::class, options={"id"="cgcId"})
     * @Method({"GET"})
     */
    public function __invoke(ContactsGroupForm $cgf, ContactsGroupContact $cgc)
    {
        if (!$cgc->isOptin()) {
            $this->optManager->optin($cgc);
            $this->optManager->dispatchOptInConfirmed($cgc, $cgf);
            $this->registry->getManager()->flush();
        }

        return new RedirectResponse(
            $this->router->generate('contacts_group_forms_optin_confirmed', ['id' => $cgf->getId()], UrlGeneratorInterface::ABSOLUTE_URL)
        );
    }
}
