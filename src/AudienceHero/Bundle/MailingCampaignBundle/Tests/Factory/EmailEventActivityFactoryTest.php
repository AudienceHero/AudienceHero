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

use AudienceHero\Bundle\CoreBundle\Entity\User;
use AudienceHero\Bundle\MailingCampaignBundle\Entity\Email;
use AudienceHero\Bundle\MailingCampaignBundle\Entity\EmailEvent;
use AudienceHero\Bundle\MailingCampaignBundle\Entity\Mailing;
use AudienceHero\Bundle\MailingCampaignBundle\Entity\MailingRecipient;
use AudienceHero\Bundle\MailingCampaignBundle\Factory\EmailEventActivityFactory;
use PHPUnit\Framework\TestCase;

class EmailEventActivityFactoryTest extends TestCase
{
    /** @var EmailEventActivityFactory */
    private $factory;

    public function setUp()
    {
        $this->factory = new EmailEventActivityFactory();
    }

    public function testFromEmailEvent()
    {
        $owner = new User();
        $mailing = new Mailing();
        $mailing->setOwner($owner);

        $mr = new MailingRecipient();
        $mr->setOwner($owner);
        $mr->setMailing($mailing);

        $email = new Email();
        $email->setOwner($owner);
        $email->setMailingRecipient($mr);

        $emailEvent = new EmailEvent();
        $emailEvent->setEvent('foobar');
        $emailEvent->setEmail($email);

        $activity = $this->factory->createFromEmailEvent($emailEvent);
        $this->assertSame($owner, $activity->getOwner());
        $this->assertSame('foobar', $activity->getType());
        $this->assertSame([
            'mailing_recipients' => $mr,
            'mailings' => $mailing,
        ], $activity->getSubjects());
    }
}
