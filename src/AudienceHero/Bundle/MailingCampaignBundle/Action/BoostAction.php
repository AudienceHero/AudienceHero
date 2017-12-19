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

use AudienceHero\Bundle\MailingCampaignBundle\Entity\Campaign;
use AudienceHero\Bundle\MailingCampaignBundle\Entity\Mailing;
use AudienceHero\Bundle\MailingCampaignBundle\Factory\MailingFactory;
use AudienceHero\Bundle\MailingCampaignBundle\Queue\MailingProducer;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * SendReminderAction.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
class BoostAction
{
    /**
     * @var RegistryInterface
     */
    private $registry;
    /**
     * @var MailingProducer
     */
    private $producer;
    /**
     * @var MailingFactory
     */
    private $mailingFactory;

    public function __construct(MailingFactory $mailingFactory, RegistryInterface $registry, MailingProducer $producer)
    {
        $this->registry = $registry;
        $this->producer = $producer;
        $this->mailingFactory = $mailingFactory;
    }

    /**
     * @Route("/api/mailings/{id}/boost", name="api_mailings_boost", defaults={"_api_resource_class"=Mailing::class, "_api_item_operation_name"="boost"})
     * @Method("PUT")
     * @Security("has_role('ROLE_USER') and is_granted('IS_OWNER', data)")
     */
    public function __invoke(Mailing $data)
    {
        if (in_array($data->getStatus(), [Campaign::STATUS_DRAFT, Campaign::STATUS_PENDING], true)) {
            throw new BadRequestHttpException(sprintf('Mailing cannot be boosted as it is in status %s.', $data->getStatus()));
        }

        if ($data->getBoostMailing()) {
            throw new BadRequestHttpException('Mailing cannot be boosted as it is already boosted');
        }

        $this->mailingFactory->createBoost($data);
        $data->getBoostMailing()->setStatus(Campaign::STATUS_PENDING);

        $this->registry->getManager()->flush();
        $this->producer->boostMailing($data);

        return $data;
    }
}
