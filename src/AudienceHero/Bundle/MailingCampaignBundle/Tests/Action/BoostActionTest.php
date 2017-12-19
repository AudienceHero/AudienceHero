<?php

/*
 * This file is part of the AudienceHero project.
 *
 * (c) Marc Weistroff <marc@weistroff.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AudienceHero\Bundle\MailingCampaignBundle\Tests\Action;

use AudienceHero\Bundle\MailingCampaignBundle\Action\BoostAction;
use AudienceHero\Bundle\MailingCampaignBundle\Entity\Mailing;
use AudienceHero\Bundle\MailingCampaignBundle\Factory\MailingFactory;
use AudienceHero\Bundle\MailingCampaignBundle\Queue\MailingProducer;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Bridge\Doctrine\RegistryInterface;

class BoostActionTest extends TestCase
{
    private $registry;
    private $producer;
    private $mailingFactory;
    private $action;
    private $mailing;

    public function setUp()
    {
        $this->registry = $this->prophesize(RegistryInterface::class);
        $this->producer = $this->prophesize(MailingProducer::class);
        $this->mailingFactory = $this->prophesize(MailingFactory::class);

        $this->action = new BoostAction($this->mailingFactory->reveal(), $this->registry->reveal(), $this->producer->reveal());
        $this->mailing = new Mailing();
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\BadRequestHttpException
     * @expectedExceptionMessage Mailing cannot be boosted as it is in status draft.
     */
    public function testInvokeThrowsExceptionIfCampaignIsDraft()
    {
        $this->mailing->setStatus(Mailing::STATUS_DRAFT);
        $this->action->__invoke($this->mailing);
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\BadRequestHttpException
     * @expectedExceptionMessage Mailing cannot be boosted as it is in status pending.
     */
    public function testInvokeThrowsExceptionIfCampaignIsPending()
    {
        $this->mailing->setStatus(Mailing::STATUS_PENDING);
        $this->action->__invoke($this->mailing);
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\BadRequestHttpException
     * @expectedExceptionMessage Mailing cannot be boosted as it is already boosted
     */
    public function testInvokeThrowsExceptionIfCampaignIsAlreadyBoosted()
    {
        $this->mailing->setStatus(Mailing::STATUS_DELIVERING);
        $this->mailing->setBoostMailing(new Mailing());
        $this->action->__invoke($this->mailing);
    }

    public function testInvoke()
    {
        $mailing = $this->mailing;
        $mailing->setStatus(Mailing::STATUS_DELIVERING);
        $boost = new Mailing();

        $this->mailingFactory->createBoost($this->mailing)->shouldBeCalled()->will(function() use($mailing, $boost) {
            $mailing->setBoostMailing($boost);
        });
        $em = $this->prophesize(EntityManagerInterface::class);
        $em->flush()->shouldBeCalled();
        $this->registry->getManager()->willReturn($em->reveal());
        $this->producer->boostMailing($mailing)->shouldBeCalled();
        $this->assertSame($mailing, $this->action->__invoke($mailing));
        $this->assertSame(Mailing::STATUS_PENDING, $boost->getStatus());
    }
}
