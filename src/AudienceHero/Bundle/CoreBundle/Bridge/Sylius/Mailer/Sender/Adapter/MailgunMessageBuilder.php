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

namespace AudienceHero\Bundle\CoreBundle\Bridge\Sylius\Mailer\Sender\Adapter;

use Sylius\Component\Mailer\Model\EmailInterface;
use Sylius\Component\Mailer\Renderer\RenderedEmail;

/**
 * MailgunMessageBuilder.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
class MailgunMessageBuilder
{
    public function build(array $recipients, string $senderAddress, string $senderName, RenderedEmail $renderedEmail, EmailInterface $email, array $data, array $attachments = []): \Swift_Message
    {
        $m = new \Swift_Message();
        $m->setFrom([$senderAddress => $senderName])
          ->setSubject($renderedEmail->getSubject());

        foreach ($recipients as $address => $name) {
            $m->addTo($address, $name);
        }

        return $m;
    }
}
