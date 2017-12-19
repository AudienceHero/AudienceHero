<?php

namespace AudienceHero\Bundle\ImageServerBundle\Twig;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * ImageServerTwigExtension.
 *
 * @author Marc Weistroff <marc@weistroff.net> 
 */
class ImageServerTwigExtension extends \Twig_Extension
{
    /** @var UrlGeneratorInterface */
    private $router;

    public function __construct(UrlGeneratorInterface $router)
    {
        $this->router = $router;
    }

    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('img', array($this, 'generateImageUrl')),
        ];
    }

    public function generateImageUrl($url, $size, array $options): string
    {
        return $this->router->generate('audience_hero_img_show', array_merge([
            'url' => $url,
            'size' => $size, ], $options), UrlGeneratorInterface::ABSOLUTE_URL);
    }
}
