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

namespace AudienceHero\Bundle\FileBundle\Action;

use ApiPlatform\Core\Bridge\Symfony\Validator\Exception\ValidationException;
use AudienceHero\Bundle\FileBundle\Entity\File;
use AudienceHero\Bundle\FileBundle\Factory\FileFactory;
use AudienceHero\Bundle\FileBundle\Filesystem\FileSystemInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UploadAction
{
    /** @var FileFactory */
    private $fileFactory;
    /** @var ValidatorInterface */
    private $validator;
    /** @var RegistryInterface */
    private $registry;
    /** @var SerializerInterface */
    private $serializer;
    /**
     * @var FileSystemInterface
     */
    private $filesystem;

    public function __construct(RegistryInterface $registry, FileFactory $fileFactory, FileSystemInterface $filesystem, ValidatorInterface $validator, SerializerInterface $serializer)
    {
        $this->registry = $registry;
        $this->fileFactory = $fileFactory;
        $this->validator = $validator;
        $this->serializer = $serializer;
        $this->filesystem = $filesystem;
    }

    /**
     * @Route("/api/upload", name="files_upload")
     * @Method({"POST"})
     * @Security("has_role('ROLE_USER')")
     */
    public function __invoke(Request $request)
    {
        $request->setRequestFormat('json');
        $uploadedFile = $request->files->get('file');
        if (!$uploadedFile) {
            throw new BadRequestHttpException('No file present in the request');
        }

        $file = $this->fileFactory->createFromUploadedFile($uploadedFile);
        $constraints = $this->validator->validate($file, null, ['upload']);
        if (0 !== count($constraints)) {
            throw new ValidationException($constraints);
        }

        $this->filesystem->copy($uploadedFile, $file);

        $em = $this->registry->getManager();
        $em->persist($file);
        $em->flush();

        $content = $this->serializer->serialize($file, 'jsonld', ['enable_max_depth' => true, 'groups' => ['read']]);

        return new Response($content, 200, ['Content-Type' => 'application/ld+json']);
    }
}
