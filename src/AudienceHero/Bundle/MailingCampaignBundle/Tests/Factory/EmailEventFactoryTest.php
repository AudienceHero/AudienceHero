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

namespace AudienceHero\Bundle\MailingCampaignBundle\Tests\Factory;

use AudienceHero\Bundle\MailingCampaignBundle\Entity\EmailEvent;
use AudienceHero\Bundle\MailingCampaignBundle\Factory\EmailEventFactory;
use PHPUnit\Framework\TestCase;

class EmailEventFactoryTest extends TestCase
{
    /** @var EmailEventFactory */
    private $factory;

    public function setUp()
    {
        $this->factory = new EmailEventFactory();
    }

    public function testCreateFromMailgunWebhookReturnsNullIfDntIsPassed()
    {
        $this->assertNull(
            $this->factory->createFromMailgunWebhook(
                [
                    'event' => 'clicked',
                    'timestamp' => '1466675737',
                    'ip' => '192.168.0.1',
                    'url' => 'https://audiencehero.org/?__dnt=1',
                ]
            )
        );
    }

    public function testCreateFromMailgunWebhook()
    {
        $event = $this->factory->createFromMailgunWebhook([
            'event' => 'clicked',
            'ip' => '192.168.0.1',
            'timestamp' => '1466675737',
            'url' => 'https://audiencehero.org/my_link',
        ]);
        $this->assertNotNull($event);
        $this->assertSame('1466675737', $event->getCreatedAt()->format('U'));
        $this->assertSame('192.168.0.1', $event->getIp());
        $this->assertSame(EmailEvent::EVENT_CLICK, $event->getEvent());
    }

    /**
     * @dataProvider provideTestCreateFromMailgunWebhookWithDifferentEvents
     */
    public function testCreateFromMailgunWebhookWithDifferentEvents(array $data, string $expectedEvent)
    {
        $event = $this->factory->createFromMailgunWebhook($data);
        $this->assertSame($expectedEvent, $event->getEvent());
    }

    public function provideTestCreateFromMailgunWebhookWithDifferentEvents()
    {
        return [
            [
                ['event' => 'opened', 'ip' => '192.168.0.1', 'timestamp' => '1466675737'],
                EmailEvent::EVENT_OPEN,
            ],
            [
                ['event' => 'bounced', 'timestamp' => '1466675737'],
                EmailEvent::EVENT_HARD_BOUNCE,
            ],
            [
                ['event' => 'dropped', 'timestamp' => '1466675737'],
                EmailEvent::EVENT_REJECT,
            ],
            [
                ['event' => 'complained', 'timestamp' => '1466675737'],
                EmailEvent::EVENT_SPAM,
            ],
        ];
    }

    public function testCreateFromMailgunWebhookWithRealWorldJson()
    {
    }
}
