<?php

namespace AudienceHero\Bundle\ImageServerBundle\Server;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * ServerInterface.
 *
 * @author Marc Weistroff <marc@weistroff.net> 
 */
interface ServerInterface
{
    /**
     * Returns a response based on parameters defined in r
     *
     * @param array $options
     * @return Response
     */
    public function serve(Request $request): Response;
}
