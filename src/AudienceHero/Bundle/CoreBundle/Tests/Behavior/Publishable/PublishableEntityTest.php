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

namespace AudienceHero\Bundle\CoreBundle\Tests\Behavior\Publishable;

use AudienceHero\Bundle\CoreBundle\Behavior\Publishable\PublishableEntity;
use AudienceHero\Bundle\CoreBundle\Behavior\Publishable\PublishableInterface;
use PHPUnit\Framework\TestCase;

class PublishableEntityTest extends TestCase
{
    /** @var PublishableInterface */
    private $entity;

    public function setUp()
    {
        $this->entity = new class() implements PublishableInterface
        {
            use PublishableEntity;
        };
    }

    public function testAccessorsDefaultState()
    {
        $this->assertNull($this->entity->getPrivacy());
        $this->assertNull($this->entity->getScheduledAt());
        $this->assertFalse($this->entity->isPrivacyPrivate());
        $this->assertFalse($this->entity->isPrivacyPublic());
        $this->assertFalse($this->entity->isPrivacyUnlisted());
        $this->assertFalse($this->entity->isPrivacyScheduled());
        $this->assertFalse($this->entity->isTimeForPublication());
    }

    public function testPrivacyAccessors()
    {
        $this->entity->setPrivacy(PublishableInterface::PRIVACY_PRIVATE);
        $this->assertSame(PublishableInterface::PRIVACY_PRIVATE, $this->entity->getPrivacy());
    }

    public function testScheduledAtAccessors()
    {
        $date = new \DateTime();

        $this->entity->setScheduledAt($date);
        $this->assertSame($date, $this->entity->getScheduledAt());
    }

    public function testBooleanMethods()
    {
        $this->entity->setPrivacy(PublishableInterface::PRIVACY_PRIVATE);
        $this->assertTrue($this->entity->isPrivacyPrivate());

        $this->entity->setPrivacy(PublishableInterface::PRIVACY_PUBLIC);
        $this->assertTrue($this->entity->isPrivacyPublic());

        $this->entity->setPrivacy(PublishableInterface::PRIVACY_UNLISTED);
        $this->assertTrue($this->entity->isPrivacyUnlisted());

        $this->entity->setPrivacy(PublishableInterface::PRIVACY_SCHEDULED);
        $this->assertTrue($this->entity->isPrivacyScheduled());
    }

    /**
     * @dataProvider provideIsTimeForPublication
     *
     * @param string         $privacy
     * @param \DateTime|null $date
     * @param bool           $result
     */
    public function testIsTimeForPublication(string $privacy, ?\DateTime $date, bool $result)
    {
        $this->entity->setPrivacy($privacy);
        if ($date) {
            $this->entity->setScheduledAt($date);
        }
        $this->assertSame($result, $this->entity->isTimeForPublication());
    }

    public function provideIsTimeForPublication()
    {
        return [
            [PublishableInterface::PRIVACY_PRIVATE, null, false],
            [PublishableInterface::PRIVACY_PUBLIC, null, false],
            [PublishableInterface::PRIVACY_UNLISTED, null, false],
            [PublishableInterface::PRIVACY_SCHEDULED, null, true],
            [PublishableInterface::PRIVACY_SCHEDULED, new \DateTime('-1 week'), true],
            [PublishableInterface::PRIVACY_SCHEDULED, new \DateTime('+1 week'), false],
        ];
    }
}
