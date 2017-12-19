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
use AudienceHero\Bundle\MailingCampaignBundle\Queue\MailingProducer;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * SendAction.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
class SendAction
{
    /**
     * @var RegistryInterface
     */
    private $registry;
    /**
     * @var MailingProducer
     */
    private $producer;

    public function __construct(RegistryInterface $registry, MailingProducer $producer)
    {
        $this->registry = $registry;
        $this->producer = $producer;
    }

    /**
     * @Route("/api/mailings/{id}/send", name="api_mailings_send", defaults={"_api_resource_class"=Mailing::class, "_api_item_operation_name"="send"})
     * @Method("PUT")
     * @Security("has_role('ROLE_USER') and is_granted('IS_OWNER', data)")
     */
    public function __invoke(Mailing $data)
    {
        if (Campaign::STATUS_DRAFT !== $data->getStatus()) {
            throw new BadRequestHttpException('Mailing cannot be sent as its status is not draft');
        }

        $data->setStatus(Campaign::STATUS_PENDING);
        $this->registry->getManager()->flush();
        $this->producer->sendMailing($data);

        return $data;
    }
}
