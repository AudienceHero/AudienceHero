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

namespace AudienceHero\Bundle\MailingCampaignBundle\Factory;

use AudienceHero\Bundle\MailingCampaignBundle\Entity\EmailEvent;

/**
 * EmailEventFactory.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
class EmailEventFactory
{
    public function createFromMailgunWebhook(array $data): ?EmailEvent
    {
        $e = new EmailEvent();

        $event = $data['event'];
        if (isset($data['ip'])) {
            $e->setIp($data['ip']);
        }
        $e->setData($data);
        $e->setCreatedAt(\DateTime::createFromFormat('U', $data['timestamp']));

        switch ($event) {
            case 'clicked':
                if (false !== strpos($data['url'], '__dnt=')) {
                    return null;
                }
                $e->setEvent(EmailEvent::EVENT_CLICK);
                break;
            case 'opened':
                $e->setEvent(EmailEvent::EVENT_OPEN);
                break;
            case 'bounced':
                $e->setEvent(EmailEvent::EVENT_HARD_BOUNCE);
                break;
            case 'dropped':
                $e->setEvent(EmailEvent::EVENT_REJECT);
                break;
            case 'complained':
                $e->setEvent(EmailEvent::EVENT_SPAM);
                break;
            default:
                return null;
        }

        return $e;
    }
}
