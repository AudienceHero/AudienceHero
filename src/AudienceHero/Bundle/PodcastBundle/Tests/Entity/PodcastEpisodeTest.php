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

namespace AudienceHero\Bundle\PodcastBundle\Tests\Entity;

use AudienceHero\Bundle\PodcastBundle\Entity\PodcastEpisode;
use PHPUnit\Framework\TestCase;

class PodcastEpisodeTest extends TestCase
{
    public function testGetPublishedAt()
    {
        $e = new PodcastEpisode();
        $now = new \DateTime();
        $e->setPublishedAt($now);
        $this->assertSame($e->getPublishedAt(), $now);

        $e = new PodcastEpisode();
        $e->setCreatedAt(new \DateTime());
        $this->assertSame($e->getPublishedAt(), $e->getCreatedAt());

        $e = new PodcastEpisode();
        $e->setPrivacy(\AudienceHero\Bundle\CoreBundle\Behavior\Publishable\PublishableInterface::PRIVACY_PUBLIC);
        $scheduledAt = new \DateTime('+1 month');
        $e->setScheduledAt($scheduledAt);
        $this->assertSame($scheduledAt, $e->getPublishedAt());
    }
}
