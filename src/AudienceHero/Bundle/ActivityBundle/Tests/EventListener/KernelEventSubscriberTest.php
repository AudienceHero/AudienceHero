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

namespace AudienceHero\Bundle\ActivityBundle\Tests\EventListener;

use AudienceHero\Bundle\ActivityBundle\Entity\Visitor;
use AudienceHero\Bundle\ActivityBundle\EventListener\VisitorKernelEventSubscriber;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;

class KernelEventSubscriberTest extends TestCase
{
    /** @var ObjectProphecy */
    private $kernel;

    public function setUp()
    {
        $this->kernel = $this->prophesize(HttpKernelInterface::class)->reveal();
    }

    public function testGetSubscribedEvents()
    {
        $events = VisitorKernelEventSubscriber::getSubscribedEvents();

        $this->assertArrayHasKey(KernelEvents::REQUEST, $events);
        $this->assertArrayHasKey(KernelEvents::RESPONSE, $events);
        $this->assertEquals([['onKernelRequest', 17]], $events[KernelEvents::REQUEST]);
        $this->assertEquals([['onKernelResponse', 17]], $events[KernelEvents::RESPONSE]);
    }

    public function testDoNotTrackIsRespectedWhenPresent()
    {
        $request = new Request();
        $response = new Response();
        $this->assertNull($request->attributes->get('do_not_track'));
        $request->headers->set('DNT', '1');

        $listener = new VisitorKernelEventSubscriber();

        $event = new GetResponseEvent($this->kernel, $request, HttpKernelInterface::MASTER_REQUEST);
        $listener->onKernelRequest($event);
        $this->assertTrue($request->attributes->get('do_not_track'));

        $event = new FilterResponseEvent($this->kernel, $request, HttpKernelInterface::MASTER_REQUEST, $response);
        $listener->onKernelResponse($event);
        $this->assertEmpty($response->headers->getCookies());
    }

    public function testUserIsTrackedUponFirstVisit()
    {
        $request = new Request();
        $response = new Response();

        $listener = new VisitorKernelEventSubscriber();
        $event = new GetResponseEvent($this->kernel, $request, HttpKernelInterface::MASTER_REQUEST);
        $listener->onKernelRequest($event);
        $uuid = $request->attributes->get('visitor');
        $this->assertNotEmpty($uuid);

        $event = new FilterResponseEvent($this->kernel, $request, HttpKernelInterface::MASTER_REQUEST, $response);
        $listener->onKernelResponse($event);

        $cookies = $response->headers->getCookies();
        $this->assertCount(1, $cookies);
        /** @var \Symfony\Component\HttpFoundation\Cookie $cookie */
        $cookie = $cookies[0];
        $this->assertSame($uuid, $cookie->getValue());
        $this->assertSame(VisitorKernelEventSubscriber::COOKIE_NAME, $cookie->getName());
    }

    public function testUserIsTrackedWithSameUuidUponSubsequentVisit()
    {
        $request = new Request();
        $response = new Response();

        $uuid = '28165011-fffe-4c03-9202-58d3086114e9';
        $request->cookies->set(VisitorKernelEventSubscriber::COOKIE_NAME, $uuid);

        $listener = new VisitorKernelEventSubscriber();
        $event = new GetResponseEvent($this->kernel, $request, HttpKernelInterface::MASTER_REQUEST);
        $listener->onKernelRequest($event);
        $this->assertSame($uuid, $request->attributes->get('visitor'));

        $event = new FilterResponseEvent($this->kernel, $request, HttpKernelInterface::MASTER_REQUEST, $response);
        $listener->onKernelResponse($event);

        // No need to set the cookie as it is already there
        $cookies = $response->headers->getCookies();
        $this->assertCount(0, $cookies);
    }
}
