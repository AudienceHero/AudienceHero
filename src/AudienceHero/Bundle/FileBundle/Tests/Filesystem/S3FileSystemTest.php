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

namespace AudienceHero\Bundle\FileBundle\Tests\Filesystem;

use AudienceHero\Bundle\FileBundle\Entity\File;
use AudienceHero\Bundle\FileBundle\Filesystem\S3FileSystem;
use Aws\CommandInterface;
use Aws\S3\S3Client;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;

class S3FileSystemTest extends TestCase
{
    /** ObjectProphecy */
    private $s3Client;

    public function setUp()
    {
        $this->s3Client = $this->prophesize(S3Client::class);
    }

    public function testCopy()
    {
    }

    public function testResolveUrl()
    {
        $file = new File();
        $file->setName('my_file');
        $file->setExtension('jpg');
        $file->setRemoteName('my_file.jpg');
        $file->setContentType('image/jpeg');

        $command = $this->prophesize(CommandInterface::class)->reveal();
        $request = $this->prophesize(RequestInterface::class);
        $request->getUri()->shouldBeCalled()->willReturn('http://foo');

        $this->s3Client->getCommand('GetObject', [
            'Bucket' => 'my_bucket',
            'Key' => 'upload/my_file.jpg',
            'ResponseContentType' => 'image/jpeg',
            'ResponseContentDisposition' => 'attachement; filename="my_file.jpg"',
        ])->willReturn($command)->shouldBeCalled();

        $this->s3Client->createPresignedRequest($command, '+20 minutes')
            ->willReturn($request->reveal())
            ->shouldBeCalled();

        $filesystem = new S3FileSystem($this->s3Client->reveal(), 'my_bucket');
        $this->assertSame('http://foo', $filesystem->resolveUrl($file));
    }

    public function testRemove()
    {
        $file = new File();
        $file->setName('my_file');
        $file->setExtension('jpg');
        $file->setRemoteName('my_file.jpg');
        $file->setContentType('image/jpeg');

        $this->s3Client->deleteObject([
            'Bucket' => 'my_bucket',
            'Key' => 'my_upload_dir/my_file.jpg',
        ])->shouldBeCalled();

        $filesystem = new S3FileSystem($this->s3Client->reveal(), 'my_bucket', 'my_upload_dir');
        $filesystem->remove($file);
    }
}
