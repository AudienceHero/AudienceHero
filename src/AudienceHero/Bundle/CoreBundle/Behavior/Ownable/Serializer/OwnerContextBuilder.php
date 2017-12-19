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

namespace AudienceHero\Bundle\CoreBundle\Behavior\Ownable\Serializer;

use ApiPlatform\Core\DataProvider\PaginatorInterface;
use ApiPlatform\Core\Serializer\SerializerContextBuilderInterface;
use AudienceHero\Bundle\CoreBundle\Behavior\Ownable\Security\Authorization\Voter\OwnableVoter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * OwnerContextBuilder.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
class OwnerContextBuilder implements SerializerContextBuilderInterface
{
    private $decorated;
    private $authorizationChecker;

    public function __construct(SerializerContextBuilderInterface $decorated, AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->decorated = $decorated;
        $this->authorizationChecker = $authorizationChecker;
    }

    public function createFromRequest(Request $request, bool $normalization, array $extractedAttributes = null): array
    {
        $context = [];
        if ($extractedAttributes) {
            $context = $this->decorated->createFromRequest($request, $normalization, $extractedAttributes);
        }
        $subject = $request->attributes->get('data');

        if ($subject instanceof PaginatorInterface) {
            $context['groups'][] = 'private_read';
        } elseif ($this->authorizationChecker->isGranted(OwnableVoter::ATTRIBUTE, $subject)) {
            $context['groups'][] = 'private_read';
        }

        return $context;
    }
}
