<?php

namespace AudienceHero\Bundle\ImageServerBundle\Tests\Twig;

use AudienceHero\Bundle\ImageServerBundle\Twig\ImageServerTwigExtension;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ImageServerTwigExtensionTest extends TestCase
{
    public function testGenerateImageUrl()
    {
        $router = $this->getMockBuilder('Symfony\Component\Routing\Generator\UrlGeneratorInterface')
                       ->getMock();

        $url = 'https://example.com/img.jpg';
        $size = '400x0';
        $expected = 'https://foo.bar';

        $router->expects($this->once())
               ->method('generate')
               ->with('audience_hero_img_show', ['url' => $url, 'size' => $size, 'crop' => 'none'], UrlGeneratorInterface::ABSOLUTE_URL)
               ->will($this->returnValue($expected));

        $extension = new ImageServerTwigExtension($router);

        $result = $extension->generateImageUrl($url, $size, ['crop' => 'none']);
        $this->assertSame($expected, $result);
    }
}
