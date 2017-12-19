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

namespace AudienceHero\Bundle\MailingCampaignBundle\Tests\Action;

use AudienceHero\Bundle\MailingCampaignBundle\Action\SendAction;
use AudienceHero\Bundle\MailingCampaignBundle\Entity\Campaign;
use AudienceHero\Bundle\MailingCampaignBundle\Entity\Mailing;
use AudienceHero\Bundle\MailingCampaignBundle\Queue\MailingProducer;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Bridge\Doctrine\RegistryInterface;

class SendActionTest extends TestCase
{
    /** @var ObjectProphecy */
    private $producer;
    /** @var ObjectProphecy */
    private $registry;
    /** @var ObjectProphecy */
    private $manager;

    public function setUp()
    {
        $this->manager = $this->prophesize(EntityManagerInterface::class);
        $this->registry = $this->prophesize(RegistryInterface::class);
        $this->producer = $this->prophesize(MailingProducer::class);
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\BadRequestHttpException
     * @dataProvider provideTestSendDoesNotProcessMailingIfStatusIsNotDraft
     */
    public function testSendDoesNotProcessMailingIfStatusIsNotDraft($status)
    {
        $mailing = new Mailing();
        $mailing->setStatus($status);

        $this->registry->getManager()->shouldNotBeCalled();
        $this->producer->sendMailing()->shouldNotBeCalled();
        $action = new SendAction($this->registry->reveal(), $this->producer->reveal());
        $action($mailing);
    }

    public function provideTestSendDoesNotProcessMailingIfStatusIsNotDraft()
    {
        return [
            [Campaign::STATUS_PENDING],
            [Campaign::STATUS_DELIVERING],
            [Campaign::STATUS_DELIVERED],
        ];
    }

    public function testSend()
    {
        $mailing = new Mailing();
        $mailing->setStatus(Campaign::STATUS_DRAFT);

        $this->manager->flush()->shouldBeCalled();
        $this->registry->getManager()->willReturn($this->manager->reveal())->shouldBeCalled();
        $this->producer->sendMailing($mailing)->shouldBeCalled();
        $action = new SendAction($this->registry->reveal(), $this->producer->reveal());
        $action($mailing);

        $this->assertSame(Campaign::STATUS_PENDING, $mailing->getStatus());
    }
}
