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

namespace AudienceHero\Bundle\CoreBundle\Mailer;

use AudienceHero\Bundle\CoreBundle\Entity\Person;
use Sylius\Component\Mailer\Sender\SenderInterface;

/**
 * TransactionalMailer.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
class TransactionalMailer
{
    /**
     * @var SenderInterface
     */
    private $sender;

    public function __construct(SenderInterface $sender)
    {
        $this->sender = $sender;
    }

    public function send(string $code, Person $owner, array $data, ?string $toEmail): void
    {
        $to = $toEmail ? $toEmail : $owner->getEmail();
        $recipient = [$to => $owner->getUsername()];
        $data = array_merge(['owner' => $owner], $data);

        $this->sender->send($code, $recipient, $data);
    }
}
