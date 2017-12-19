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

namespace AudienceHero\Bundle\FileBundle\Tests\Factory;

use AudienceHero\Bundle\CoreBundle\Entity\Person;
use AudienceHero\Bundle\FileBundle\Factory\FileFactory;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class FileFactoryTest extends TestCase
{
    /** @var ObjectProphecy */
    private $tokenStorage;
    /** @var FileFactory */
    private $factory;

    public function setUp()
    {
        $this->tokenStorage = $this->prophesize(TokenStorageInterface::class);
        $this->factory = new FileFactory();
    }

    public function testCreateFileFromUploadedFile()
    {
        $person = $this->prophesize(Person::class)->reveal();
        $token = $this->prophesize(TokenInterface::class);
        $token->getUser()->willReturn($person);
        $this->tokenStorage->getToken()->willReturn($token->reveal());
        $this->factory->setTokenStorage($this->tokenStorage->reveal());

        $path = __DIR__.'/../../Resources/fixtures/assets/free.png';
        $uploadedFile = new UploadedFile($path, 'free.png', 'image/png', filesize($path), null, true);

        $file = $this->factory->createFromUploadedFile($uploadedFile);
        $this->assertSame($person, $file->getOwner());
        $this->assertSame('image/png', $file->getContentType());
        $this->assertSame('png', $file->getExtension());
        $this->assertSame('free', $file->getName());
        $this->assertSame(18677, $file->getSize());
        $this->assertNotEmpty($file->getRemoteName());
        $this->assertSame(219, $file->getPublicMetadataValue('width'));
        $this->assertSame(218, $file->getPublicMetadataValue('height'));
        $this->assertSame(1.0, $file->getPublicMetadataValue('ratio'));
    }

    public function testCreateFileWithApplicationOctetStreamDetectedMP3()
    {
        $person = $this->prophesize(Person::class)->reveal();
        $token = $this->prophesize(TokenInterface::class);
        $token->getUser()->willReturn($person);
        $this->tokenStorage->getToken()->willReturn($token->reveal());
        $this->factory->setTokenStorage($this->tokenStorage->reveal());

        $path = __DIR__.'/../../Resources/fixtures/assets/Drunken Party.mp3';
        $uploadedFile = new UploadedFile($path, 'mime-type-not-detected.mp3', 'application/octet-stream', filesize($path), null, true);

        $file = $this->factory->createFromUploadedFile($uploadedFile);
        $this->assertSame($person, $file->getOwner());
        $this->assertSame('audio/mpeg', $file->getContentType());
        $this->assertSame('mp3', $file->getExtension());
        $this->assertSame('mime-type-not-detected', $file->getName());
        $this->assertSame(4852541, $file->getSize());
        $this->assertNotEmpty($file->getRemoteName());
        $this->assertNull($file->getPublicMetadataValue('width'));
        $this->assertNull($file->getPublicMetadataValue('height'));
        $this->assertNull($file->getPublicMetadataValue('ratio'));
    }
}
