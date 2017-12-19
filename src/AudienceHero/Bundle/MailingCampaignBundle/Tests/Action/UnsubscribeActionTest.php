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

use AudienceHero\Bundle\ContactBundle\Entity\Contact;
use AudienceHero\Bundle\ContactBundle\Entity\ContactsGroup;
use AudienceHero\Bundle\ContactBundle\Entity\ContactsGroupContact;
use AudienceHero\Bundle\ContactBundle\Manager\OptManager;
use AudienceHero\Bundle\CoreBundle\Entity\User;
use AudienceHero\Bundle\MailingCampaignBundle\Action\UnsubscribeAction;
use AudienceHero\Bundle\MailingCampaignBundle\Entity\Mailing;
use AudienceHero\Bundle\MailingCampaignBundle\Entity\MailingRecipient;
use AudienceHero\Bundle\MailingCampaignBundle\Repository\MailingRecipientRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\HttpFoundation\Response;

class UnsubscribeActionTest extends TestCase
{
    /** @var ObjectProphecy */
    private $twig;
    /** @var ObjectProphecy */
    private $manager;
    /** @var ObjectProphecy */
    private $registry;
    /** @var ObjectProphecy */
    private $repository;
    /** @var ObjectProphecy */
    private $optManager;

    public function setUp()
    {
        $this->twig = $this->prophesize(\Twig_Environment::class);
        $this->manager = $this->prophesize(EntityManagerInterface::class);
        $this->registry = $this->prophesize(RegistryInterface::class);
        $this->repository = $this->prophesize(MailingRecipientRepository::class);
        $this->optManager = $this->prophesize(OptManager::class);
    }

    private function getActionInstance(): UnsubscribeAction
    {
        return new UnsubscribeAction(
            $this->twig->reveal(),
            $this->registry->reveal(),
            $this->repository->reveal(),
            $this->optManager->reveal()
        );
    }

    public function testNoOpInTestMode()
    {
        $this->repository->find(Argument::any())->shouldNotBeCalled();
        $this->optManager->optout(Argument::any())->shouldNotBeCalled();

        $this->twig->render('AudienceHeroMailingCampaignBundle:action:unsubscribe.html.twig')
                   ->shouldBeCalled();

        $mailing = new Mailing();
        $action = $this->getActionInstance();
        $result = $action($mailing, 'test');
        $this->assertInstanceOf(Response::class, $result);
        $this->assertSame(200, $result->getStatusCode());
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @expectedExceptionMessage No MailingRecipient with id my_bad_id
     */
    public function test404WhenNoMailingRecipientIsFound()
    {
        $this->repository->find('my_bad_id')
                         ->shouldBeCalled()
                         ->willReturn(null)
        ;

        $this->optManager->optout(Argument::any())->shouldNotBeCalled();

        $this->twig->render('AudienceHeroMailingCampaignBundle:action:unsubscribe.html.twig')
            ->shouldNotBeCalled();

        $mailing = new Mailing();
        $action = $this->getActionInstance();
        $action($mailing, 'my_bad_id');
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @expectedExceptionMessage MailingRecipient with id my_id is not associated to Mailing mailing_id_1
     */
    public function test404WhenMailingRecipientDoesNotMatchMailing()
    {
        $mailing1 = $this->prophesize(Mailing::class);
        $mailing1->getId()->shouldBeCalled()->willReturn('mailing_id_1');

        $mailing2 = $this->prophesize(Mailing::class);
        $mailing2->getId()->shouldBeCalled()->willReturn('mailing_id_2');
        $mailing2->getRecipients()->willReturn(new ArrayCollection());
        $mailing2->getOwner()->shouldBeCalled()->willReturn(new User());

        $mailingRecipient = new MailingRecipient();
        $mailingRecipient->setMailing($mailing2->reveal());

        $this->repository->find('my_id')
            ->shouldBeCalled()
            ->willReturn($mailingRecipient)
        ;

        $this->optManager->optout(Argument::any())->shouldNotBeCalled();

        $this->twig->render('AudienceHeroMailingCampaignBundle:action:unsubscribe.html.twig')
            ->shouldNotBeCalled();

        $action = $this->getActionInstance();
        $action($mailing1->reveal(), 'my_id');
    }

    public function testFullOp()
    {
        $user = new User();
        $mailing = $this->prophesize(Mailing::class);
        $mailing->getId()->shouldBeCalled()->willReturn('mailing_id_1');
        $mailing->getRecipients()->willReturn(new ArrayCollection());
        $mailing->getOwner()->shouldBeCalled()->willReturn($user);

        $cgc = new ContactsGroupContact();
        $contact = new Contact();
        $contact->setOwner($user);
        $contact->setName('foo');
        $contact->setEmail('foo@example.com');
        $cgc->setContact($contact);
        $group = new ContactsGroup();
        $group->setOwner($user);
        $cgc->setGroup($group);

        $mailingRecipient = new MailingRecipient();
        $mailingRecipient->setMailing($mailing->reveal());
        $mailingRecipient->setContactsGroupContact($cgc);

        $this->repository->find('my_id')
            ->shouldBeCalled()
            ->willReturn($mailingRecipient)
        ;

        $this->manager->flush()->shouldBeCalled();
        $this->registry->getManager()->willReturn($this->manager->reveal())->shouldBeCalled();

        $this->optManager->optout($cgc)->shouldBeCalled();

        $this->twig->render('AudienceHeroMailingCampaignBundle:action:unsubscribe.html.twig')
            ->shouldBeCalled();

        $action = $this->getActionInstance();
        $action($mailing->reveal(), 'my_id');
    }
}
