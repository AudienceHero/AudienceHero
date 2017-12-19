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

namespace AudienceHero\Bundle\CoreBundle\Action;

use AudienceHero\Bundle\CoreBundle\Search\Searcher;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * SearchAction.
 *
 * @author Marc Weistroff <marc@weistroff.net>
 */
class SearchAction
{
    /**
     * @var Searcher
     */
    private $searcher;
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;
    /**
     * @var SerializerInterface
     */
    private $serializer;

    public function __construct(Searcher $searcher, TokenStorageInterface $tokenStorage, SerializerInterface $serializer)
    {
        $this->searcher = $searcher;
        $this->tokenStorage = $tokenStorage;
        $this->serializer = $serializer;
    }

    /**
     * @Route("/api/search", name="audience_hero_search")
     * @Method({"GET"})
     * @Security("has_role('ROLE_USER')")
     */
    public function __invoke(Request $request)
    {
        $request->setRequestFormat('json');

        $q = $request->query->get('q', '');

        $user = $this->tokenStorage->getToken()->getUser();
        $results = $this->searcher->search($user, $q);

        $content = $this->serializer->serialize($results, 'jsonld', ['enable_max_depth' => true, 'groups' => ['read']]);

        return new Response($content, 200, ['Content-Type' => 'application/ld+json']);
    }
}
