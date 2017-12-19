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

namespace AudienceHero\Bundle\CoreBundle\Behavior\Ownable\Repository\Specification;

use AudienceHero\Bundle\CoreBundle\Entity\Person;
use Happyr\DoctrineSpecification\BaseSpecification;
use Happyr\DoctrineSpecification\Spec;

/**
 * OwnedBy.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
class OwnedBy extends BaseSpecification
{
    private $owner;

    public function __construct(Person $owner, $dqlAlias = null)
    {
        $this->owner = $owner;

        parent::__construct($dqlAlias);
    }

    public function getSpec()
    {
        return Spec::AndX(
            Spec::eq('owner', $this->owner)
        );
    }
}
