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

namespace AudienceHero\Bundle\MailingCampaignBundle\Repository\Specification;

use Happyr\DoctrineSpecification\BaseSpecification;
use Happyr\DoctrineSpecification\Spec;

/**
 * EmailIsIdentifiableBy.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
class EmailIsIdentifiableBy extends BaseSpecification
{
    /**
     * @var string
     */
    private $id;

    public function __construct(string $id, $dqlAlias = null)
    {
        $this->id = $id;

        parent::__construct($dqlAlias);
    }

    public function getSpec()
    {
        return Spec::andX(
            Spec::eq('mandrillId', $this->id)
        );
    }
}
