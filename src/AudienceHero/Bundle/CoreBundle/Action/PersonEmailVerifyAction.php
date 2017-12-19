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

use AudienceHero\Bundle\CoreBundle\Entity\PersonEmail;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * PersonEmailVerifyAction.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
class PersonEmailVerifyAction
{
    /**
     * @Route("/api/person_emails/{id}/verify",name="api_person_emails_verify",
     *     defaults={"_api_resource_class"=Resources::class, "_api_item_operation_name"="verify"})
     * @Security("has_role('ROLE_USER') and is_granted('IS_OWNER', data)")
     */
    public function __invoke(PersonEmail $data)
    {
        if ($data->getIsVerified()) {
            return $data;
        }

        if ($data->getToken() !== $data->getConfirmationToken()) {
            throw new BadRequestHttpException('Cannot confirm email address');
        }

        $data->setConfirmationToken(null);
        $data->setIsVerified(true);

        return $data;
    }
}
