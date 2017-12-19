<?php

/*
 * This file is part of the AudienceHero project.
 *
 * (c) Marc Weistroff <marc@weistroff.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AudienceHero\Bundle\ImageServerBundle\Action;
use AudienceHero\Bundle\ImageServerBundle\Domain\ImgRequest;
use AudienceHero\Bundle\ImageServerBundle\Server\ServerInterface;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * ShowAction
 * @author Marc Weistroff <marc@weistroff.net>
 */
class ShowAction
{
    /**
     * @var ServerInterface
     */
    private $server;

    public function __construct(ServerInterface $server)
    {
        $this->server = $server;
    }

    /**
     * @Route("/i", name="audience_hero_img_show")
     * @Route("/i/{size}/{crop}/{url}", name="audience_hero_img_show_alt", defaults={"crop": "none"})
     * @Method({"GET", "HEAD"})
     */
    public function __invoke(Request $request): Response
    {
        if ($request->isMethod('HEAD')) {
            return new Response('', 200);
        }

        $url = $request->query->get('url');
        if (!$url) {
            throw new BadRequestHttpException('The url query parameter is required.');
        }
        $request->query->set('url', urldecode($url));

        return $this->server->serve($request);
    }
}