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

namespace AudienceHero\Bundle\MailingCampaignBundle\Action;

use AudienceHero\Bundle\ContactBundle\Manager\OptManager;
use AudienceHero\Bundle\MailingCampaignBundle\Entity\Mailing;
use AudienceHero\Bundle\MailingCampaignBundle\Entity\MailingRecipient;
use AudienceHero\Bundle\MailingCampaignBundle\Repository\MailingRecipientRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * UnsubscribeAction.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
class UnsubscribeAction
{
    /**
     * @var MailingRecipientRepository
     */
    private $repository;

    /**
     * @var OptManager
     */
    private $optManager;
    /**
     * @var \Twig_Environment
     */
    private $twig;
    /**
     * @var RegistryInterface
     */
    private $registry;

    public function __construct(\Twig_Environment $twig, RegistryInterface $registry, MailingRecipientRepository $repository, OptManager $optManager)
    {
        $this->repository = $repository;
        $this->optManager = $optManager;
        $this->twig = $twig;
        $this->registry = $registry;
    }

    /**
     * @Route("/mailings/{id}/unsubscribe/{recipientId}", name="mailings_optout")
     * @Method({"GET"})
     */
    public function __invoke(Mailing $mailing, string $recipientId)
    {
        if ('test' !== $recipientId) {
            /** @var MailingRecipient $mr */
            $mr = $this->repository->find($recipientId);
            if (!$mr) {
                throw new NotFoundHttpException(sprintf('No MailingRecipient with id %s', $recipientId));
            }

            if ($mr->getMailing()->getId() !== $mailing->getId()) {
                throw new NotFoundHttpException(sprintf('MailingRecipient with id %s is not associated to Mailing %s', $recipientId, $mailing->getId()));
            }

            $this->optManager->optout($mr->getContactsGroupContact());
            $this->registry->getManager()->flush();
        }

        return new Response($this->twig->render('AudienceHeroMailingCampaignBundle:action:unsubscribe.html.twig'));
    }
}
