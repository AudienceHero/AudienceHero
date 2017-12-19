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

use AudienceHero\Bundle\CoreBundle\Bridge\Symfony\HttpFoundation\EmptyJsonLdResponse;
use AudienceHero\Bundle\PromoBundle\Entity\Promo;
use AudienceHero\Bundle\PromoBundle\Mailer\MailerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * SendPreviewAction.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
class SendPreviewAction
{
    /**
     * @var MailerInterface
     */
    private $mailer;
    /**
     * @var RegistryInterface
     */
    private $registry;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * @Route("/api/promos/{id}/send-preview", name="api_promos_send_preview",
     *     defaults={"_api_resource_class"=Promo::class, "_api_item_operation_name"="send_preview"})
     * @Method("PUT")
     * @Security("has_role('ROLE_USER') and is_granted('IS_OWNER', data)")
     */
    public function __invoke(Promo $data)
    {
        $this->mailer->sendPreview($data, $data->getTestRecipient());

        return new EmptyJsonLdResponse();
    }
}
