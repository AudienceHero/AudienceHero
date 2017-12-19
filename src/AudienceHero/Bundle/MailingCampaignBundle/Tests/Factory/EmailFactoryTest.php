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
use AudienceHero\Bundle\MailingCampaignBundle\Entity\MailingRecipient;
use AudienceHero\Bundle\MailingCampaignBundle\Factory\EmailFactory;
use PHPUnit\Framework\TestCase;

class EmailFactoryTest extends TestCase
{
    /** @var EmailFactory */
    private $factory;

    public function setUp()
    {
        $this->factory = new EmailFactory();
    }

    public function testCreate()
    {
        $user = new User();
        $mr = new MailingRecipient();
        $mr->setOwner($user);

        $email = $this->factory->create($mr);
        $this->assertInstanceOf(Email::class, $email);
        $this->assertSame($user, $email->getOWner());
        $this->assertSame($mr, $email->getMailingRecipient());
        $this->assertSame($email, $mr->getEmail());
    }
}
