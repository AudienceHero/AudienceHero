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

use AudienceHero\Bundle\FileBundle\Action\UploadAction;
use AudienceHero\Bundle\FileBundle\Entity\File;
use AudienceHero\Bundle\FileBundle\Factory\FileFactory;
use AudienceHero\Bundle\FileBundle\Filesystem\FileSystemInterface;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UploadActionTest extends TestCase
{
    /** @var ObjectProphecy */
    private $fileFactory;
    /** @var ObjectProphecy */
    private $filesystem;
    /** @var ObjectProphecy */
    private $registry;
    /** @var ValidatorInterface */
    private $validator;
    /** @var ValidatorInterface */
    private $serializer;

    public function setUp()
    {
        $this->registry = $this->prophesize(RegistryInterface::class);
        $this->fileFactory = $this->prophesize(FileFactory::class);
        $this->filesystem = $this->prophesize(FileSystemInterface::class);
        $this->validator = $this->prophesize(ValidatorInterface::class);
        $this->serializer = $this->prophesize(SerializerInterface::class);
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\BadRequestHttpException
     * @expectedExceptionMessage No file present in the request
     */
    public function testActionThrowsExceptionIfNoFileIsProvided()
    {
        $action = $this->getActionInstance();
        $action(new Request());
    }

    private function getActionInstance(): UploadAction
    {
        return new UploadAction($this->registry->reveal(), $this->fileFactory->reveal(), $this->filesystem->reveal(), $this->validator->reveal(), $this->serializer->reveal());
    }

    /**
     * @expectedException \ApiPlatform\Core\Bridge\Symfony\Validator\Exception\ValidationException
     */
    public function testActionFailsIfFileIsnotValidated()
    {
        $path = __FILE__;
        $uploadedFile = new UploadedFile($path, 'UploadActionTest.php', 'text/plain', filesize($path), null, true);

        $request = new Request();
        $request->files->set('file', $uploadedFile);

        $file = new File();
        $this->fileFactory->createFromUploadedFile($uploadedFile)->willReturn($file)->shouldBeCalled();

        $violation = $this->prophesize(ConstraintViolationInterface::class)->reveal();
        $this->validator->validate($file, null, ['upload'])
                        ->willReturn(new ConstraintViolationList([$violation]))
                        ->shouldBeCalled();

        $this->filesystem->copy($uploadedFile, $file)->shouldNotBeCalled();

        $action = $this->getActionInstance();
        $action($request);
    }

    public function testActionUploadsFile()
    {
        $path = __DIR__.'/../../Resources/fixtures/assets/free.png';
        $uploadedFile = new UploadedFile($path, 'free.png', 'image/png', filesize($path), null, true);

        $request = new Request();
        $request->files->set('file', $uploadedFile);

        $file = new File();
        $file->setRemoteName('remote_name.png');
        $this->fileFactory->createFromUploadedFile($uploadedFile)->willReturn($file)->shouldBeCalled();

        $this->validator->validate($file, null, ['upload'])->willReturn(new ConstraintViolationList())->shouldBeCalled();

        $this->filesystem->copy($uploadedFile, $file)->shouldBeCalled();

        $em = $this->prophesize(EntityManagerInterface::class);
        $em->persist($file)->shouldBeCalled();
        $em->flush()->shouldBeCalled();

        $this->registry->getManager()->shouldBeCalled()->willReturn($em->reveal());
        $action = $this->getActionInstance();
        $action($request);
    }
}
