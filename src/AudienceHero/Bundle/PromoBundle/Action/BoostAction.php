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

use AudienceHero\Bundle\MailingCampaignBundle\Entity\Campaign;
use AudienceHero\Bundle\MailingCampaignBundle\Factory\MailingFactory;
use AudienceHero\Bundle\PromoBundle\Entity\Promo;
use AudienceHero\Bundle\PromoBundle\Queue\PromoProducer;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * BoostAction.
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
     * @var PromoProducer
     */
    private $producer;
    /**
     * @var MailingFactory
     */
    private $mailingFactory;

    public function __construct(MailingFactory $mailingFactory, RegistryInterface $registry, PromoProducer $producer)
    {
        $this->registry = $registry;
        $this->producer = $producer;
        $this->mailingFactory = $mailingFactory;
    }

    /**
     * @Route("/api/promos/{id}/boost", name="api_promos_boost", defaults={"_api_resource_class"=Promo::class, "_api_item_operation_name"="boost"})
     * @Method("PUT")
     * @Security("has_role('ROLE_USER') and is_granted('IS_OWNER', data)")
     */
    public function __invoke(Promo $data)
    {
        if (in_array($data->getMailing()->getStatus(), [Campaign::STATUS_DRAFT, Campaign::STATUS_PENDING], true)) {
            throw new BadRequestHttpException(sprintf('Promo cannot be boosted as its status is %s', $data->getMailing()->getStatus()));
        }

        if ($data->getMailing()->getBoostMailing()) {
            throw new BadRequestHttpException('Promo cannot be boosted as it is already boosted');
        }

        $this->mailingFactory->createBoost($data->getMailing());
        $data->getMailing()->getBoostMailing()->setStatus(Campaign::STATUS_PENDING);
        $this->registry->getManager()->flush();
        $this->producer->boostPromo($data);

        return $data;
    }
}
