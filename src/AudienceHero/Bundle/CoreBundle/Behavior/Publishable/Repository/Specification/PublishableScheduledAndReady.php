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

namespace AudienceHero\Bundle\CoreBundle\Behavior\Publishable\Repository\Specification;

use AudienceHero\Bundle\CoreBundle\Behavior\Publishable\PublishableInterface;
use Happyr\DoctrineSpecification\BaseSpecification;
use Happyr\DoctrineSpecification\Spec;
use Happyr\DoctrineSpecification\Specification\Specification;

/**
 * PublishableSpecification.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
class PublishableScheduledAndReady extends BaseSpecification
{
    /**
     * @return Specification
     */
    public function getSpec()
    {
        $now = new \DateTime('now', new \DateTimeZone('UTC'));

        return Spec::andX(
            Spec::eq('privacy', PublishableInterface::PRIVACY_SCHEDULED),
            Spec::gt('scheduledAt', $now)
        );
    }
}
