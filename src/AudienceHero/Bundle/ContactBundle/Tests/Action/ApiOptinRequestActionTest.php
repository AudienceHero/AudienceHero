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

use AudienceHero\Bundle\ContactBundle\Action\ApiOptinRequestAction;
use AudienceHero\Bundle\ContactBundle\Entity\Contact;
use AudienceHero\Bundle\ContactBundle\Entity\ContactsGroup;
use AudienceHero\Bundle\ContactBundle\Entity\ContactsGroupContact;
use AudienceHero\Bundle\ContactBundle\Entity\ContactsGroupForm;
use AudienceHero\Bundle\ContactBundle\Factory\ContactFactory;
use AudienceHero\Bundle\ContactBundle\Manager\ContactManager;
use AudienceHero\Bundle\ContactBundle\Manager\OptManager;
use AudienceHero\Bundle\CoreBundle\Entity\User;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\HttpFoundation\Request;

class ApiOptinRequestActionTest extends TestCase
{
    /** @var ObjectProphecy */
    private $contactFactory;
    /** @var ObjectProphecy */
    private $contactManager;
    /** @var ObjectProphecy */
    private $optManager;
    /** @var ContactsGroupForm */
    private $cgf;

    public function setUp()
    {
        $this->contactFactory = $this->prophesize(ContactFactory::class);
        $this->contactManager = $this->prophesize(ContactManager::class);
        $this->optManager = $this->prophesize(OptManager::class);

        $this->cgf = new ContactsGroupForm();
        $this->cg = new ContactsGroup();
        $this->cgf->setOwner(new User());
        $this->cgf->setContactsGroup($this->cg);
    }

    private function getInstance(): ApiOptinRequestAction
    {
        return new ApiOptinRequestAction($this->contactFactory->reveal(), $this->contactManager->reveal(), $this->optManager->reveal());
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\BadRequestHttpException
     * @expectedExceptionMessage  Malformed JSON request
     */
    public function testInvokeThrowsExceptionIfMessageIsMalformed()
    {
        $request = new Request([], [], [], [], [], [], '{foobar');
        $this->getInstance()($request, $this->cgf);
    }

    public function testInvoke()
    {
        $request = new Request([], [], [], [], [], [], '{}');

        $c1 = new Contact();
        $this->contactFactory->createFromJson('{}', $this->cgf->getOwner())
            ->shouldBeCalled()
            ->willReturn($c1);

        $c2 = new Contact();
        $this->contactManager->add($c1)->shouldBeCalled()->willReturn($c2);
        $cgc = new ContactsGroupContact();
        $this->contactManager->addToGroup($c2, $this->cgf->getContactsGroup())->shouldBeCalled()->willReturn($cgc);
        $this->optManager->dispatchOptInRequest($cgc, $this->cgf)->shouldBeCalled();

        $this->assertSame($this->cgf, $this->getInstance()($request, $this->cgf));
    }
}
