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

namespace AudienceHero\Bundle\CoreBundle\Tests\Behavior\Publishable\Repository\Specification;

use AudienceHero\Bundle\CoreBundle\Behavior\Publishable\Repository\Specification\PublishableScheduledAndReady;
use Happyr\DoctrineSpecification\Specification\Specification;
use PHPUnit\Framework\TestCase;

class PublishableScheduledAndReadyTest extends TestCase
{
    public function testSpecification()
    {
        $specification = new PublishableScheduledAndReady();
        $spec = $specification->getSpec();

        $this->assertInstanceOf(Specification::class, $spec);
        // TODO: test more!
    }
}
