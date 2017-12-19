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

namespace AudienceHero\Bundle\PromoBundle\Action;

use AudienceHero\Bundle\CoreBundle\Behavior\Ownable\Security\Authorization\Voter\OwnableVoter;
use AudienceHero\Bundle\CoreBundle\Validator\Assert;
use AudienceHero\Bundle\FileBundle\Factory\DownloadUrlResponseFactory;
use AudienceHero\Bundle\FileBundle\Filesystem\FileSystemInterface;
use AudienceHero\Bundle\PromoBundle\Domain\DownloadUrlResponse;
use AudienceHero\Bundle\PromoBundle\Entity\Promo;
use AudienceHero\Bundle\PromoBundle\Entity\PromoRecipient;
use AudienceHero\Bundle\PromoBundle\Factory\PromoRecipientFactory;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * FeedbackAction.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
class FeedbackAction
{
    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;
    /**
     * @var RegistryInterface
     */
    private $registry;
    /**
     * @var FileSystemInterface
     */
    private $fileSystem;
    /**
     * @var DownloadUrlResponseFactory
     */
    private $responseFactory;
    /**
     * @var PromoRecipientFactory
     */
    private $promoRecipientFactory;

    public function __construct(RegistryInterface $registry, AuthorizationCheckerInterface $authorizationChecker, PromoRecipientFactory $promoRecipientFactory, DownloadUrlResponseFactory $responseFactory)
    {
        $this->authorizationChecker = $authorizationChecker;
        $this->registry = $registry;
        $this->responseFactory = $responseFactory;
        $this->promoRecipientFactory = $promoRecipientFactory;
    }

    /**
     * @Route("/api/promos/{id}/feedback",
     *        name="api_promos_feedback",
     *        defaults={
     *          "_api_resource_class"=Promo::class,
     *          "_api_item_operation_name"="feedback",
     *        })
     * @Method("POST")
     */
    public function __invoke(Request $request, Promo $data)
    {
        Assert::validJSONRequest($request);
        $feedback = json_decode($request->getContent(), true);

        $httpResponse = $this->responseFactory->create($data->getDownload());

        // In preview mode, we reject unauthorized people
        if ('preview' === ($recipientId = $feedback['recipientId'])) {
            if (!$this->authorizationChecker->isGranted(OwnableVoter::ATTRIBUTE, $data)) {
                throw new BadRequestHttpException('Malformed recipientId');
            }

            return $httpResponse;
        }

        $unserialized = $this->promoRecipientFactory->createFromJson($request->getContent(), $data);
        $em = $this->registry->getManager();

        /** @var PromoRecipient $pr */
        $pr = $em->find(PromoRecipient::class, $recipientId);
        if (!$pr) {
            throw new BadRequestHttpException('PromoRecipient not found.');
        }
        if ($pr->getOwner() !== $data->getOwner()) {
            throw new BadRequestHttpException('Owners are incompatible');
        }
        $pr->setFeedback($unserialized->getFeedback());
        $pr->setFavoriteTrack($unserialized->getFavoriteTrack());
        $pr->setRating($unserialized->getRating());
        $em->flush();

        return $httpResponse;
    }
}
