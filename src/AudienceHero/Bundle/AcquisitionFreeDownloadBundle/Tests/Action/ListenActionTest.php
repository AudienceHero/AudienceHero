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

namespace AudienceHero\Bundle\AcquisitionFreeDownloadBundle\Tests\Action;

use AudienceHero\Bundle\AcquisitionFreeDownloadBundle\Action\ListenAction;
use AudienceHero\Bundle\AcquisitionFreeDownloadBundle\Entity\AcquisitionFreeDownload;
use AudienceHero\Bundle\AcquisitionFreeDownloadBundle\Events\AcquisitionFreeDownloadEvent;
use AudienceHero\Bundle\AcquisitionFreeDownloadBundle\Events\AcquisitionFreeDownloadEvents;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Response;

class ListenActionTest extends TestCase
{
    /** @var ObjectProphecy */
    private $twig;
    /** @var ObjectProphecy */
    private $eventDispatcher;

    public function setUp()
    {
        $this->twig = $this->prophesize(\Twig_Environment::class);
        $this->eventDispatcher = $this->prophesize(EventDispatcherInterface::class);
    }

    public function testActionDispatchHitEvent()
    {
        $afd = new AcquisitionFreeDownload();

        $this->twig->render('frontoffice.html.twig')
            ->shouldBeCalled()
            ->willReturn('foobar');

        $this->eventDispatcher->dispatch(
            AcquisitionFreeDownloadEvents::HIT,
            Argument::that(function ($event) use ($afd) {
                return $event instanceof AcquisitionFreeDownloadEvent &&
                    $event->getAcquisitionFreeDownload() === $afd;
            })
        );

        $action = new ListenAction(
            $this->twig->reveal(),
            $this->eventDispatcher->reveal()
        );

        $response = $action($afd);
        $this->assertInstanceOf(Response::class, $response);
        $this->assertSame('foobar', $response->getContent());
    }
}
