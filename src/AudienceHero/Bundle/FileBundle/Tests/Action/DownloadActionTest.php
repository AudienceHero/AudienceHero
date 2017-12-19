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

namespace AudienceHero\Bundle\FileBundle\Tests\Action;

use AudienceHero\Bundle\FileBundle\Action\DownloadAction;
use AudienceHero\Bundle\FileBundle\Entity\File;
use AudienceHero\Bundle\FileBundle\Filesystem\FileSystemInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

class DownloadActionTest extends \PHPUnit_Framework_TestCase
{
    public function testDownload()
    {
        $file = new File();

        $downloader = $this->prophesize(FileSystemInterface::class);
        $downloader->resolveUrl($file)->shouldBeCalled()->willReturn('http://foo');

        $action = new DownloadAction($downloader->reveal());
        $response = $action($file);
        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertSame('http://foo', $response->headers->get('location'));
    }
}
