<?php

namespace AudienceHero\Bundle\ImageServerBundle\Server;

use Doctrine\Common\Cache\Cache;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * CacheServer.
 *
 * @author Marc Weistroff <marc@weistroff.net> 
 */
class CacheServer implements ServerInterface
{
    private $server;
    private $cache;

    public function __construct(ServerInterface $server, Cache $cache)
    {
        $this->server = $server;
        $this->cache = $cache;
    }

    public function serve(Request $request): Response
    {
        $metaKey = sha1(json_encode($request->query->all()));
        $fileKey = sprintf('%s-file', $metaKey);

        if ($this->cache->contains($metaKey) && $this->cache->contains($fileKey)) {
            $meta = json_decode($this->cache->fetch($metaKey), true);
            $file = $this->cache->fetch($fileKey);

            $now = new \DateTime();
            $expires = new \DateTime($meta['expires']);
            if (!$meta || $now > $expires) {
                $this->cache->delete($metaKey);
                $this->cache->delete($fileKey);
            } else {
                return new Response($file, 200, [
                    'Expires' => $meta['expires'],
                    'Content-Type' => $meta['content_type'],
                    'X-AudienceHero-Image-Server-Cache-Hit' => 1,
                ]);
            }
        }

        // check if hash is present in kv cache
        // if hash is present in kv cache, fetch response in AmazonS3 and send it to server
        $response = $this->server->serve($request);
        if ($response->getStatusCode() >= 300) {
            return $response;
        }

        $this->cache->save($metaKey, json_encode([
            'content_type' => $response->headers->get('Content-Type'),
            'expires' => $response->headers->get('Expires'),
        ]));

        $this->cache->save($fileKey, $response->getContent());

        return $response;
    }
}
