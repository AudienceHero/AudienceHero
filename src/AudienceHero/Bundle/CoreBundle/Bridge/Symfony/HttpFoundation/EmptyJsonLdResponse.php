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

namespace AudienceHero\Bundle\CoreBundle\Bridge\Symfony\HttpFoundation;

use Symfony\Component\HttpFoundation\Response;

/**
 * EmptyResponse.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
final class EmptyJsonLdResponse extends Response
{
    public function __construct(int $statusCode = 200)
    {
        parent::__construct(json_encode([]), $statusCode, ['Content-Type' => 'application/ld+json']);
    }
}
