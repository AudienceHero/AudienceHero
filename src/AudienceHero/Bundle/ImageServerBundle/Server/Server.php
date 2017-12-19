<?php

namespace AudienceHero\Bundle\ImageServerBundle\Server;

use AudienceHero\Bundle\ImageServerBundle\Loader\LoaderInterface;
use AudienceHero\Bundle\ImageServerBundle\Transformer\ChainTransformer;
use AudienceHero\Bundle\ImageServerBundle\Transformer\TransformerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * ImageServer.
 *
 * @author Marc Weistroff <marc@weistroff.net> 
 */
class Server implements ServerInterface
{
    private $loader;
    private $transformer;

    public function __construct(LoaderInterface $loader, ChainTransformer $transformer)
    {
        $this->loader = $loader;
        $this->transformer = $transformer;
    }

    public function serve(Request $request): Response
    {
        $options = [];
        $url = $request->query->get('url');
        $options['url'] = $url;
        $image = $this->loader->load($url);
        $options['content_type'] = $image->getImagick()->getImageMimeType();

        $options = $this->transformer->getOptionsResolver()->resolve(array_merge(
            $options,
            $this->transformer->extractOptions($request)
        ));
        $this->transformer->transform($image, $options);

        switch ($options['content_type']) {
        case 'image/x-jpeg':
        case 'image/jpeg':
            $content = $image->get('jpeg');
            $options['content_type'] = 'image/jpeg';
            break;
        case 'image/x-png':
        case 'image/png':
            $size = $image->getImagick()->getImageLength();
            if ($size > 750 * 1024) {
                $options['content_type'] = 'image/jpeg';
                $content = $image->get('jpeg');
                $options['content_type'] = 'image/jpeg';
            } else {
                $content = $image->get('png');
                $options['content_type'] = 'image/png';
            }
            break;
        case 'image/x-gif':
        case 'image/gif':
            $content = $image->get('gif', ['flatten' => false]);
            $options['content_type'] = 'image/gif';
            break;
        default:
            throw new \RuntimeException(sprintf('I do not know how to handle content type: %s', $options['content_type']));
        }

        $response = new Response($content, 200, [
            'Content-Type' => $options['content_type'],
            'Content-Length' => strlen($content),
        ]);

        $response->setExpires(new \DateTime('+1 year'));
        $response->setMaxAge(86400);

        return $response;
    }
}
