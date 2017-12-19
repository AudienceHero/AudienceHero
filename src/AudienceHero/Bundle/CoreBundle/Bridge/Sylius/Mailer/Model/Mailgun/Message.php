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

namespace AudienceHero\Bundle\CoreBundle\Bridge\Sylius\Mailer\Model\Mailgun;

use Mailgun\Messages\MessageBuilder;

/**
 * Message.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
class Message extends MessageBuilder
{
    private $id;

    public function setId($id)
    {
        $this->id = $id;
        $this->addCustomData('audiencehero_message_id', ['id' => $id]);
    }

    public function getId()
    {
        return $this->id;
    }

    public function getTags()
    {
        return isset($this->message['o:tag']) ? $this->message['o:tag'] : [];
    }

    public function addTo($email, $name = '')
    {
        $this->addToRecipient($email, ['full_name' => $name]);
    }

    public function setFrom($email, $name)
    {
        $this->setFromAddress($email, ['full_name' => $name]);
    }

    public function getFrom()
    {
        return $this->message['from'];
    }

    public function getTo()
    {
        return $this->message['to'];
    }

    public function setText($text)
    {
        $this->setTextBody($text);
    }

    public function setTrackClicks($enabled)
    {
        $this->setClickTracking($enabled);
    }

    public function getTrackClicks()
    {
        return 'yes' === $this->message['o:tracking-clicks'] ? true : false;
    }

    public function setTrackOpens($enabled)
    {
        $this->setOpenTracking($enabled);
    }

    public function getTrackOpens()
    {
        return 'yes' === $this->message['o:tracking-opens'] ? true : false;
    }

    public function setHtml($html)
    {
        $this->setHtmlBody($html);
    }

    public function getHtml()
    {
        return $this->message['html'];
    }

    public function getSubject()
    {
        return $this->message['subject'];
    }

    public function getText()
    {
        return $this->message['text'];
    }
}
