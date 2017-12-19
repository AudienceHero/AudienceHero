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

namespace AudienceHero\Bundle\ContactBundle\Repository\Specification;

use Happyr\DoctrineSpecification\BaseSpecification;
use Happyr\DoctrineSpecification\Spec;

/**
 * EmailOrPhone.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
class EmailOrPhone extends BaseSpecification
{
    /**
     * @var null|string
     */
    private $email;
    /**
     * @var null|string
     */
    private $phone;

    public function __construct(?string $email, ?string $phone, $dqlAlias = null)
    {
        $this->email = $email;
        $this->phone = $phone;

        parent::__construct($dqlAlias);
    }

    public function getSpec()
    {
        return Spec::orX(
            Spec::eq('email', $this->email),
            Spec::eq('phone', $this->phone)
        );
    }
}
