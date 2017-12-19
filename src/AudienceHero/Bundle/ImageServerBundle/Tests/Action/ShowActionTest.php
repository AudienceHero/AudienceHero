<?php

/*
 * This file is part of the AudienceHero project.
 *
 * (c) Marc Weistroff <marc@weistroff.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AudienceHero\Bundle\ImageServerBundle\Tests\Action;

use AudienceHero\Bundle\ImageServerBundle\Action\ShowAction;
use AudienceHero\Bundle\ImageServerBundle\Server\ServerInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ShowActionTest extends TestCase
{
    /** @var ObjectProphecy */
    private $server;
    /** @var ShowAction */
    private $action;

    public function setUp()
    {
        $this->server = $this->prophesize(ServerInterface::class);
        $this->action = new ShowAction($this->server->reveal());
    }

    public function testMethodHead()
    {
        $request = Request::create('/', 'HEAD');
        $action = $this->action;
        $response = $action($request);
        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('', $response->getContent());
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\BadRequestHttpException
     */
    public function testMissingUrlParameter()
    {
        $request = Request::create('/', 'GET');
        $action = $this->action;
        $action($request);
    }

    public function testInvoke()
    {
        $request = Request::create(sprintf('/?url=%s', urlencode('https://foobar.example')));
        $response = new Response();
        $this->server->serve($request)->shouldBeCalled()->willReturn($response);
        $action  = $this->action;
        $this->assertSame($response, $action($request));
        $this->assertSame('https://foobar.example', $request->query->get('url'));
    }
}
