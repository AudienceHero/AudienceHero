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

use AudienceHero\Bundle\CoreBundle\Behavior\Publishable\PublishableInterface;
use AudienceHero\Bundle\MailingCampaignBundle\Entity\Campaign;
use AudienceHero\Bundle\PromoBundle\Entity\Promo;
use AudienceHero\Bundle\PromoBundle\Queue\PromoProducer;
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
     * @var PromoProducer
     */
    private $producer;

    public function __construct(RegistryInterface $registry, PromoProducer $producer)
    {
        $this->registry = $registry;
        $this->producer = $producer;
    }

    /**
     * @Route("/api/promos/{id}/send", name="api_promos_send", defaults={"_api_resource_class"=Promo::class, "_api_item_operation_name"="send"})
     * @Method("PUT")
     * @Security("has_role('ROLE_USER') and is_granted('IS_OWNER', data)")
     */
    public function __invoke(Promo $data)
    {
        if (Campaign::STATUS_DRAFT !== $data->getMailing()->getStatus()) {
            throw new BadRequestHttpException('Promo cannot be sent as its status is not draft');
        }

        $data->getMailing()->setStatus(Campaign::STATUS_PENDING);
        $data->setPrivacy(PublishableInterface::PRIVACY_UNLISTED);
        $this->registry->getManager()->flush();
        $this->producer->sendPromo($data);

        return $data;
    }
}
