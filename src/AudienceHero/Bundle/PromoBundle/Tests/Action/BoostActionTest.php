<?php

/*
 * This file is part of the AudienceHero project.
 *
 * (c) Marc Weistroff <marc@weistroff.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AudienceHero\Bundle\PromoBundle\Tests\Action;

use AudienceHero\Bundle\MailingCampaignBundle\Entity\Mailing;
use AudienceHero\Bundle\MailingCampaignBundle\Factory\MailingFactory;
use AudienceHero\Bundle\PromoBundle\Action\BoostAction;
use AudienceHero\Bundle\PromoBundle\Entity\Promo;
use AudienceHero\Bundle\PromoBundle\Queue\PromoProducer;
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
    private $promo;

    public function setUp()
    {
        $this->registry = $this->prophesize(RegistryInterface::class);
        $this->producer = $this->prophesize(PromoProducer::class);
        $this->mailingFactory = $this->prophesize(MailingFactory::class);

        $this->action = new BoostAction($this->mailingFactory->reveal(), $this->registry->reveal(), $this->producer->reveal());
        $this->mailing = new Mailing();
        $this->promo = new Promo();
        $this->promo->setMailing($this->mailing);
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\BadRequestHttpException
     * @expectedExceptionMessage Promo cannot be boosted as its status is draft
     */
    public function testInvokeThrowsExceptionIfCampaignIsDraft()
    {
        $this->mailing->setStatus(Mailing::STATUS_DRAFT);
        $this->action->__invoke($this->promo);
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\BadRequestHttpException
     * @expectedExceptionMessage Promo cannot be boosted as its status is pending
     */
    public function testInvokeThrowsExceptionIfCampaignIsPending()
    {
        $this->mailing->setStatus(Mailing::STATUS_PENDING);
        $this->action->__invoke($this->promo);
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\BadRequestHttpException
     * @expectedExceptionMessage Promo cannot be boosted as it is already boosted
     */
    public function testInvokeThrowsExceptionIfCampaignIsAlreadyBoosted()
    {
        $this->mailing->setStatus(Mailing::STATUS_DELIVERING);
        $this->mailing->setBoostMailing(new Mailing());
        $this->action->__invoke($this->promo);
    }

    public function testInvoke()
    {
        $promo = $this->promo;
        $promo->getMailing()->setStatus(Mailing::STATUS_DELIVERING);
        $boost = new Mailing();

        $this->mailingFactory->createBoost($this->promo->getMailing())->shouldBeCalled()->will(function() use($promo, $boost) {
            $promo->getMailing()->setBoostMailing($boost);
        });
        $em = $this->prophesize(EntityManagerInterface::class);
        $em->flush()->shouldBeCalled();
        $this->registry->getManager()->willReturn($em->reveal());
        $this->producer->boostPromo($promo)->shouldBeCalled();
        $this->assertSame($promo, $this->action->__invoke($promo));
        $this->assertSame(Mailing::STATUS_PENDING, $boost->getStatus());
    }
}
