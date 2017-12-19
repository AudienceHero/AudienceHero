<?php

namespace AudienceHero\Bundle\ImageServerBundle\Tests\Server;

use AudienceHero\Bundle\ImageServerBundle\Loader\StreamLoader;
use AudienceHero\Bundle\ImageServerBundle\Server\Server;
use AudienceHero\Bundle\ImageServerBundle\Transformer\ChainTransformer;
use AudienceHero\Bundle\ImageServerBundle\Transformer\CropTransformer;
use AudienceHero\Bundle\ImageServerBundle\Transformer\ImageResizerTransformer;
use Imagine\Imagick\Imagine;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ServerTest extends TestCase
{
    /** @var Server */
    private $server;

    public function setUp()
    {
        $this->loader = new StreamLoader();
        $this->transformer = new ChainTransformer();
        $this->transformer->addTransformer(new ImageResizerTransformer());
        $this->transformer->addTransformer(new CropTransformer());

        $this->server = new Server($this->loader, $this->transformer);
    }

    public function testServe()
    {
        $request = Request::create(sprintf('/?url=%s&size=240x240', urlencode('http://lorempixel.com/288/290/')));
        $response = $this->server->serve($request);

        $this->assertTrue($response instanceof Response);
        $this->assertEquals(86400, $response->getMaxAge());

        $imagine = new Imagine();
        $image = $imagine->load($response->getContent());
        $size = $image->getSize();

        $this->assertEquals(240, $size->getWidth());
        $this->assertEquals(240, $size->getHeight());
    }

    public function testServeAndCrop()
    {
        $request = Request::create(sprintf('/?url=%s&size=0x100', urlencode('http://lorempixel.com/288/200/')));
        $response = $this->server->serve($request);

        $imagine = new Imagine();
        $image = $imagine->load($response->getContent());
        $size = $image->getSize();
        $this->assertSame(100, $size->getHeight());
        $this->assertGreaterThanOrEqual(144, $size->getWidth());

        $request = Request::create(sprintf('/?url=%s&size=0x100&crop=square', 'http://lorempixel.com/288/200/'));
        $response = $this->server->serve($request);
        $imagine = new Imagine();
        $image = $imagine->load($response->getContent());
        $size = $image->getSize();
        $this->assertSame(100, $size->getHeight());
        $this->assertSame(100, $size->getWidth());

        $request = Request::create(sprintf('/?url=%s&size=0x100&crop=square-center', 'http://lorempixel.com/288/200/'));
        $response = $this->server->serve($request);
        $imagine = new Imagine();
        $image = $imagine->load($response->getContent());
        $size = $image->getSize();
        $this->assertSame(100, $size->getHeight());
        $this->assertSame(100, $size->getWidth());
    }
}
