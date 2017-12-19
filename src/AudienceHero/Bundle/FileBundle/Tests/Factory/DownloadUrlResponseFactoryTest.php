<?php

/*
 * This file is part of the AudienceHero project.
 *
 * (c) Marc Weistroff <marc@weistroff.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AudienceHero\Bundle\FileBundle\Tests\Factory;

use AudienceHero\Bundle\FileBundle\Model\DownloadUrlResponse;
use AudienceHero\Bundle\AcquisitionFreeDownloadBundle\Entity\AcquisitionFreeDownload;
use AudienceHero\Bundle\FileBundle\Factory\DownloadUrlResponseFactory;
use AudienceHero\Bundle\FileBundle\Entity\File;
use AudienceHero\Bundle\FileBundle\Filesystem\FileSystemInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;

class DownloadUrlResponseFactoryTest extends TestCase
{
    public function testCreate()
    {
        $afd = new AcquisitionFreeDownload();
        $download = new File();
        $afd->setDownload($download);

        $fs = $this->prophesize(FileSystemInterface::class);
        $fs->resolveUrl($download)->shouldBeCalled()->willReturn('file_url');

        $serializer = $this->prophesize(SerializerInterface::class);
        $serializer->serialize(Argument::that(function(DownloadUrlResponse $downloadUrlResponse) {
            return $downloadUrlResponse->getUrl() === 'file_url';
        }), 'json')->shouldBeCalled()->willReturn('foobar');

        $factory = new \AudienceHero\Bundle\FileBundle\Factory\DownloadUrlResponseFactory($fs->reveal(), $serializer->reveal());
        $response = $factory->create($afd);

        $this->assertInstanceOf(Response::class, $response);
        $this->assertSame('foobar', $response->getContent());
        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('application/ld+json', $response->headers->get('Content-Type'));
    }
}
