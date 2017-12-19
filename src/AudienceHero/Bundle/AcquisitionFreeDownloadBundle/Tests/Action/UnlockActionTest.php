<?php

/*
 * This file is part of the AudienceHero project.
 *
 * (c) Marc Weistroff <marc@weistroff.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AudienceHero\Bundle\AcquisitionFreeDownloadBundle\Tests\Action;

use AudienceHero\Bundle\AcquisitionFreeDownloadBundle\Action\UnlockAction;
use AudienceHero\Bundle\AcquisitionFreeDownloadBundle\Entity\AcquisitionFreeDownload;
use AudienceHero\Bundle\FileBundle\Factory\DownloadUrlResponseFactory;
use AudienceHero\Bundle\AcquisitionFreeDownloadBundle\Manager\UnlockManager;
use AudienceHero\Bundle\ContactBundle\Entity\Contact;
use AudienceHero\Bundle\ContactBundle\Factory\ContactFactory;
use AudienceHero\Bundle\CoreBundle\Entity\User;
use AudienceHero\Bundle\CoreBundle\Behavior\Ownable\Security\Authorization\Voter\OwnableVoter;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class UnlockActionTest extends TestCase
{
    /** @var ObjectProphecy */
    private $responseFactory;
    /** @var ObjectProphecy */
    private $authorizationChecker;
    /** @var ObjectProphecy */
    private $contactFactory;
    /** @var ObjectProphecy */
    private $unlockManager;

    /** @var AcquisitionFreeDownload */
    private $afd;
    /** @var string */
    private $content;
    /** @var Request */
    private $request;

    public function setUp()
    {
        $this->responseFactory = $this->prophesize(DownloadUrlResponseFactory::class);
        $this->authorizationChecker = $this->prophesize(AuthorizationCheckerInterface::class);
        $this->contactFactory = $this->prophesize(ContactFactory::class);
        $this->unlockManager = $this->prophesize(UnlockManager::class);

        $this->afd = new AcquisitionFreeDownload();
        $this->afd->setOwner(new User());
        $this->content = '{}';
        $this->request = new Request(
            [],
            [],
            [],
            [],
            [],
            [],
            $this->content
        );
    }

    public function getInstance(): UnlockAction
    {
        return new UnlockAction($this->responseFactory->reveal(), $this->authorizationChecker->reveal(), $this->contactFactory->reveal(), $this->unlockManager->reveal());
    }

    public function testInvoke()
    {
        $contact = new Contact();
        $response = new Response();
        $this->responseFactory->create($this->afd->getDownload())->shouldBeCalled()->willReturn($response);
        $this->authorizationChecker->isGranted(OwnableVoter::ATTRIBUTE, $this->afd)->willReturn(false)->shouldBeCalled();
        $this->contactFactory->createFromJson($this->content, $this->afd->getOwner())->shouldBeCalled()->willReturn($contact);
        $this->unlockManager->unlock($this->afd, $contact)->shouldBeCalled();

        $this->assertSame($response, $this->getInstance()($this->request, $this->afd));
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\BadRequestHttpException
     */
    public function testInvokeThrowsExceptionOnBadJson()
    {
        $request = new Request(
            [],
            [],
            [],
            [],
            [],
            [],
            '{foobar'
        );
        $this->getInstance()($request, $this->afd);
    }

    public function testInvokeReturnsImmediatelyInCaseOfAuthenticatedRequest()
    {
        $contact = new Contact();
        $response = new Response();
        $this->responseFactory->create($this->afd->getDownload())->shouldBeCalled()->willReturn($response);
        $this->authorizationChecker->isGranted(OwnableVoter::ATTRIBUTE, $this->afd)->willReturn(true)->shouldBeCalled();
        $this->contactFactory->createFromJson($this->content, $this->afd->getOwner())->shouldNotBeCalled();
        $this->unlockManager->unlock($this->afd, $contact)->shouldNotBeCalled();

        $this->assertSame($response, $this->getInstance()($this->request, $this->afd));
    }
}
