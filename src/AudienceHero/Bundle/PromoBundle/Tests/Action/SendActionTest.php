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

namespace AudienceHero\Bundle\PromoBundle\Tests\Action;

use AudienceHero\Bundle\MailingCampaignBundle\Entity\Campaign;
use AudienceHero\Bundle\MailingCampaignBundle\Entity\Mailing;
use AudienceHero\Bundle\PromoBundle\Action\SendAction;
use AudienceHero\Bundle\PromoBundle\Entity\Promo;
use AudienceHero\Bundle\PromoBundle\Queue\PromoProducer;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Bridge\Doctrine\RegistryInterface;

class SendActionTest extends TestCase
{
    /** @var ObjectProphecy */
    private $registry;
    /** @var ObjectProphecy */
    private $producer;
    /** @var Promo */
    private $promo;
    /** @var Mailing */
    private $mailing;

    public function setUp()
    {
        $this->registry = $this->prophesize(RegistryInterface::class);
        $this->producer = $this->prophesize(PromoProducer::class);

        $this->promo = new Promo();
        $this->mailing = new Mailing();
        $this->promo->setMailing($this->mailing);
    }

    private function getActionInstance(): SendAction
    {
        return new SendAction($this->registry->reveal(), $this->producer->reveal());
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\BadRequestHttpException
     */
    public function testPromoIsNotSentIfMailingStatusIsNotDraft()
    {
        $this->mailing->setStatus(Campaign::STATUS_PENDING);
        $this->registry->getManager()->shouldNotBeCalled();
        $this->producer->sendPromo($this->promo)->shouldNotBeCalled();

        $action = $this->getActionInstance();
        $action($this->promo);
    }

    public function testPromoIsSent()
    {
        $manager = $this->prophesize(EntityManagerInterface::class);
        $manager->flush()->shouldBeCalled();
        $this->registry->getManager()->shouldBeCalled()->willReturn($manager->reveal());
        $this->producer->sendPromo($this->promo)->shouldBeCalled();

        $action = $this->getActionInstance();
        $result = $action($this->promo);
        $this->assertSame($this->promo, $result);
        $this->assertSame(Campaign::STATUS_PENDING, $this->promo->getMailing()->getStatus());
    }
}
