<?php

/*
 * This file is part of the AudienceHero project.
 *
 * (c) Marc Weistroff <marc@weistroff.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AudienceHero\Bundle\ContactBundle\Tests\Action;

use AudienceHero\Bundle\ContactBundle\Action\OptinConfirmAction;
use AudienceHero\Bundle\ContactBundle\Entity\ContactsGroupContact;
use AudienceHero\Bundle\ContactBundle\Entity\ContactsGroupForm;
use AudienceHero\Bundle\ContactBundle\Manager\OptManager;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class OptinConfirmActionTest extends TestCase
{
    /** @var ObjectProphecy */
    private $optManager;
    /** @var ObjectProphecy */
    private $registry;
    /** @var ObjectProphecy */
    private $router;
    /** @var ObjectProphecy */
    private $em;
    /** @var ObjectProphecy */
    private $cgf;
    /** @var ObjectProphecy */
    private $cgc;

    public function setUp()
    {
        $this->optManager = $this->prophesize(OptManager::class);
        $this->registry = $this->prophesize(RegistryInterface::class);
        $this->router = $this->prophesize(UrlGeneratorInterface::class);
        $this->em = $this->prophesize(EntityManagerInterface::class);

        $this->registry->getManager()->willReturn($this->em->reveal());

        $this->cgf = $this->prophesize(ContactsGroupForm::class);
        $this->cgf->getId()->willReturn('id1');
        $this->cgc = $this->prophesize(ContactsGroupContact::class);
        $this->cgc->getId()->willReturn('id2');
    }

    private function getInstance(): OptinConfirmAction
    {
        return new OptinConfirmAction(
            $this->optManager->reveal(),
            $this->registry->reveal(),
            $this->router->reveal()
        );
    }

    public function testInvokeNoOpIsAlreadyOptin()
    {
        $this->optManager->optin($this->cgc)->shouldNotBeCalled();
        $this->optManager->dispatchOptInConfirmed($this->cgc->reveal(), $this->cgf->reveal())->shouldNotBeCalled();
        $this->em->flush()->shouldNotBeCalled();
        $this->router->generate(Argument::any(), Argument::any(), UrlGeneratorInterface::ABSOLUTE_URL)
            ->shouldBeCalled()
            ->willReturn('foobar')
        ;

        $this->cgc->isOptin()->shouldBeCalled()->willReturn(true);
        /** @var RedirectResponse $response */
        $response = $this->getInstance()($this->cgf->reveal(), $this->cgc->reveal());
        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertSame('foobar', $response->getTargetUrl());
    }

    public function testInvoke()
    {
        $this->optManager->optin($this->cgc)->shouldBeCalled();
        $this->optManager->dispatchOptInConfirmed($this->cgc->reveal(), $this->cgf->reveal())->shouldBeCalled();
        $this->em->flush()->shouldBeCalled();
        $this->router->generate(Argument::any(), Argument::any(), UrlGeneratorInterface::ABSOLUTE_URL)
            ->shouldBeCalled()
            ->willReturn('foobar')
        ;

        $this->cgc->isOptin()->shouldBeCalled()->willReturn(false);
        /** @var RedirectResponse $response */
        $response = $this->getInstance()($this->cgf->reveal(), $this->cgc->reveal());
        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertSame('foobar', $response->getTargetUrl());
    }
}
