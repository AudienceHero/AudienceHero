<?php

/*
 * This file is part of the AudienceHero project.
 *
 * (c) Marc Weistroff <marc@weistroff.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AudienceHero\Bundle\CoreBundle\Validator;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * Assert
 * @author Marc Weistroff <marc@weistroff.net>
 */
class Assert
{
    /**
     * Validate the JSON content of a Request.
     * Returns true if the JSON payload is valid.
     * Throws exception if it is not valid.
     *
     * @param Request $request
     * @return bool
     * @throws \Symfony\Component\HttpKernel\Exception\BadRequestHttpException
     */
    public static function validJSONRequest(Request $request): bool
    {
        $content = $request->getContent();
        $json = json_decode($content, true);
        if (null === $json) {
            throw new BadRequestHttpException('Malformed JSON request');
        }

        return true;
    }
}