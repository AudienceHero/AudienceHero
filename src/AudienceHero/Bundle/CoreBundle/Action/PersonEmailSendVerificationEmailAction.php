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

namespace AudienceHero\Bundle\CoreBundle\Action;

use AudienceHero\Bundle\CoreBundle\Bridge\Symfony\HttpFoundation\EmptyJsonLdResponse;
use AudienceHero\Bundle\CoreBundle\Entity\PersonEmail;
use AudienceHero\Bundle\CoreBundle\Mailer\Model\PersonEmailVerificationEmail;
use AudienceHero\Bundle\CoreBundle\Mailer\TransactionalMailer;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * UserSendConfirmationMailAgainAction.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
class PersonEmailSendVerificationEmailAction
{
    /**
     * @var TransactionalMailer
     */
    private $mailer;

    public function __construct(TransactionalMailer $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * @Route(
     *     "/api/person_emails/{id}/send-verification-email",
     *     name="api_person_emails_send_verification_email",
     *     defaults={"_api_resource_class"=Resources::class, "_api_item_operation_name"="send_verification_email"})
     * @Method("PUT")
     * @Security("has_role('ROLE_USER') and is_granted('IS_OWNER', data)")
     */
    public function __invoke(PersonEmail $data)
    {
        $this->mailer->send(PersonEmailVerificationEmail::class, $data->getOwner(), ['person_email' => $data], $data->getEmail());

        return new EmptyJsonLdResponse();
    }
}
