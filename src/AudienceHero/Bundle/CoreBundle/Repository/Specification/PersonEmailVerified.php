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

namespace AudienceHero\Bundle\CoreBundle\Repository\Specification;

use Happyr\DoctrineSpecification\BaseSpecification;
use Happyr\DoctrineSpecification\Spec;

/**
 * PersonEmailVerified.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
class PersonEmailVerified extends BaseSpecification
{
    public function getSpec()
    {
        return Spec::andX(
            Spec::eq('isVerified', true)
        );
    }
}
