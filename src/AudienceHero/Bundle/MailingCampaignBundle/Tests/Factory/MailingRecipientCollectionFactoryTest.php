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

use AudienceHero\Bundle\ContactBundle\Entity\Contact;
use AudienceHero\Bundle\ContactBundle\Entity\ContactsGroup;
use AudienceHero\Bundle\ContactBundle\Entity\ContactsGroupContact;
use AudienceHero\Bundle\MailingCampaignBundle\Entity\Mailing;
use AudienceHero\Bundle\MailingCampaignBundle\Entity\MailingRecipient;
use AudienceHero\Bundle\MailingCampaignBundle\Factory\MailingRecipientCollectionFactory;
use AudienceHero\Bundle\MailingCampaignBundle\Factory\MailingRecipientFactory;
use PHPUnit\Framework\TestCase;

class MailingRecipientCollectionFactoryTest extends TestCase
{
    public function testCreateCollection()
    {
        $mailing = new Mailing();

        $withEmail = new Contact();
        $withEmail->setEmail('foobar@example.com');
        $withoutEmail = new Contact();

        $cgcm1 = $this->prophesize(ContactsGroupContact::class);
        $cgcm2 = $this->prophesize(ContactsGroupContact::class);
        $cgcm3 = $this->prophesize(ContactsGroupContact::class);
        $cgcm4 = $this->prophesize(ContactsGroupContact::class);

        $cgcm1->acceptEmails()->shouldBeCalled()->willReturn(false);
        $cgcm2->acceptEmails()->shouldBeCalled()->willReturn(true);
        $cgcm3->acceptEmails()->shouldBeCalled()->willReturn(true);
        $cgcm4->acceptEmails()->shouldBeCalled()->willReturn(true);

        $cgcm1->getContact()->shouldNotBeCalled();
        $cgcm2->getContact()->shouldBeCalled()->willReturn($withoutEmail);
        $cgcm3->getContact()->shouldBeCalled()->willReturn($withEmail);
        $cgcm4->getContact()->shouldBeCalled()->willReturn($withEmail);

        $cgc1 = $cgcm1->reveal();
        $cgc2 = $cgcm2->reveal();
        $cgc3 = $cgcm3->reveal();
        $cgc4 = $cgcm4->reveal();

        $mailingRecipientFactory = $this->prophesize(MailingRecipientFactory::class);

        $mr1 = new MailingRecipient();
        $mr2 = new MailingRecipient();

        $mailingRecipientFactory->create($mailing, $cgc1)->shouldNotBeCalled();
        $mailingRecipientFactory->create($mailing, $cgc2)->shouldNotBeCalled();
        $mailingRecipientFactory->create($mailing, $cgc3)->shouldBeCalled()->willReturn($mr1);
        $mailingRecipientFactory->create($mailing, $cgc4)->shouldBeCalled()->willReturn($mr2);

        $group = $this->prophesize(ContactsGroup::class);
        $group->getContacts()->shouldBeCalled()->willReturn([$cgc1, $cgc2, $cgc3, $cgc4]);
        $mailing->setContactsGroup($group->reveal());

        $factory = new MailingRecipientCollectionFactory($mailingRecipientFactory->reveal());
        $collection = $factory->createCollection($mailing);
        $this->assertCount(2, $collection);
        $this->assertSame($mr1, $collection[0]);
        $this->assertSame($mr2, $collection[1]);
    }
}
