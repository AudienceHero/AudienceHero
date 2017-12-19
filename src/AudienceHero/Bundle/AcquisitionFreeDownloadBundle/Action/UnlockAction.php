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

namespace AudienceHero\Bundle\AcquisitionFreeDownloadBundle\Action;

use ApiPlatform\Core\Bridge\Symfony\Validator\Exception\ValidationException;
use AudienceHero\Bundle\CoreBundle\Validator\Assert;
use AudienceHero\Bundle\FileBundle\Model\DownloadUrlResponse;
use AudienceHero\Bundle\AcquisitionFreeDownloadBundle\Entity\AcquisitionFreeDownload;
use AudienceHero\Bundle\AcquisitionFreeDownloadBundle\Events\AcquisitionFreeDownloadEvent;
use AudienceHero\Bundle\AcquisitionFreeDownloadBundle\Events\AcquisitionFreeDownloadEvents;
use AudienceHero\Bundle\FileBundle\Factory\DownloadUrlResponseFactory;
use AudienceHero\Bundle\AcquisitionFreeDownloadBundle\Manager\UnlockManager;
use AudienceHero\Bundle\ContactBundle\Entity\Contact;
use AudienceHero\Bundle\ContactBundle\Factory\ContactFactory;
use AudienceHero\Bundle\ContactBundle\Manager\ContactManager;
use AudienceHero\Bundle\ContactBundle\Manager\OptManager;
use AudienceHero\Bundle\CoreBundle\Behavior\Ownable\Security\Authorization\Voter\OwnableVoter;
use AudienceHero\Bundle\FileBundle\Filesystem\FileSystemInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * UnlockAction.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
class UnlockAction
{
    /**
     * @var DownloadUrlResponseFactory
     */
    private $responseFactory;
    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;
    /**
     * @var ContactFactory
     */
    private $contactFactory;
    /**
     * @var UnlockManager
     */
    private $unlockManager;

    public function __construct(DownloadUrlResponseFactory $responseFactory, AuthorizationCheckerInterface $authorizationChecker, ContactFactory $contactFactory, UnlockManager $unlockManager)
    {
        $this->responseFactory = $responseFactory;
        $this->authorizationChecker = $authorizationChecker;
        $this->contactFactory = $contactFactory;
        $this->unlockManager = $unlockManager;
    }

    /**
     * @Route("/api/acquisition_free_downloads/{id}/unlock",
     *        name="api_acquisition_free_downloads_unlock",
     *        defaults={"_api_resource_class"=AcquisitionFreeDownload::class, "_api_item_operation_name"="unlock"}
     * )
     * @Method("PUT")
     */
    public function __invoke(Request $request, AcquisitionFreeDownload $data)
    {
        Assert::validJSONRequest($request);

        $response = $this->responseFactory->create($data->getDownload());
        if ($this->authorizationChecker->isGranted(OwnableVoter::ATTRIBUTE, $data)) {
            return $response;
        }

        $contact = $this->contactFactory->createFromJson($request->getContent(), $data->getOwner());

        $this->unlockManager->unlock($data, $contact);

        return $response;
    }
}
