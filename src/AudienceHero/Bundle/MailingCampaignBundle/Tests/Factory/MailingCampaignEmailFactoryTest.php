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

use AudienceHero\Bundle\CoreBundle\Entity\PersonEmail;
use AudienceHero\Bundle\CoreBundle\Generator\UUIDGenerator;
use AudienceHero\Bundle\MailingCampaignBundle\Entity\Mailing;
use AudienceHero\Bundle\MailingCampaignBundle\Entity\MailingRecipient;
use AudienceHero\Bundle\MailingCampaignBundle\Factory\MailingCampaignEmailFactory;
use AudienceHero\Bundle\MailingCampaignBundle\Mailer\Model\MailingCampaignEmail;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;

class MailingCampaignEmailFactoryTest extends TestCase
{
    /** @var ObjectProphecy */
    private $generator;

    public function setUp()
    {
        $this->generator = $this->prophesize(UUIDGenerator::class);
    }

    public function testCreateClassic()
    {
        $this->generator->generate()->shouldBeCalled()->willReturn('generated_id');

        $factory = new MailingCampaignEmailFactory($this->generator->reveal());

        $pe = new PersonEmail();
        $pe->setEmail('foobar@example.com');
        $mailing = new Mailing();
        $mailing->setPersonEmail($pe);
        $mailing->setFromName('Foobar');
        $mailingRecipient = new MailingRecipient();

        $email = $factory->createClassic($mailing, $mailingRecipient);
        $this->assertInstanceOf(MailingCampaignEmail::class, $email);

        $this->assertSame('Foobar', $email->getSenderName());
        $this->assertSame('foobar@example.com', $email->getSenderAddress());
        $this->assertSame('AudienceHeroMailingCampaignBundle:mailer:classic.html.twig', $email->getTemplate());
        $this->assertTrue($email->isEnabled());
        $this->assertSame($mailing, $email->getMailing());
        $this->assertSame($mailingRecipient, $email->getMailingRecipient());
        $this->assertSame('generated_id', $email->getIdentifier());
    }
}
